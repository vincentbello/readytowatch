<?php
$numPerPage = 10; // PAGINATION CONSTANT
define('REMEMBER_ME_KEY', 'rememberme');

function getMysqli() {
	global $mysqli;
	if (!$mysqli) {
		require('mysqli_connect.php');
	}
	return $mysqli;
}

// LOGIN OPERATIONS

function onLogin($user) {
	$token = gen128BitToken();
	storeTokenForUser($user, $token);
	$cookie = $user . ":" . $token;
	$mac = hash_hmac('sha256', $cookie, REMEMBER_ME_KEY);
	$cookie .= ':' . $mac;
	setcookie('rememberme', $cookie, time()+60*60*24*100, '/');
}

function rememberMe() {
	$cookie = isset($_COOKIE['rememberme']) ? $_COOKIE['rememberme'] : '';
	if ($cookie) {
		list ($user, $token, $mac) = explode(':', $cookie);
		if ($mac !== hash_hmac('sha256', $user . ':' . $token, REMEMBER_ME_KEY)) {
		 	return "";
		}
		$userToken = fetchTokenByUsername($user);
		if (timingSafeCompare($userToken, $token)) {
			return $user;
		}
	} else {
		return "";
	}
}

function gen128BitToken() {
	return md5(uniqid(mt_rand(), true));
}

function storeTokenForUser($user, $token) {
	$mysqli = getMysqli();
	mysqli_query($mysqli, "UPDATE users SET token='$token' WHERE id={$user}");
}

function fetchTokenByUsername($user) {
	require('mysqli_connect.php');
	if($t = $mysqli->query("SELECT token FROM users WHERE id={$user}")) {
	  if ($result = mysqli_fetch_assoc($t)) {
	    return $result['token'];
	  }
	} else {
		return '';
	}
}

/**
 * A timing safe equals comparison
 *
 * To prevent leaking length information, it is important
 * that user input is always used as the second parameter.
 *
 * @param string $safe The internal (safe) value to be checked
 * @param string $user The user submitted (unsafe) value
 *
 * @return boolean True if the two strings are identical.
 */
function timingSafeCompare($safe, $user) {
    // Prevent issues if string length is 0
    $safe .= chr(0);
    $user .= chr(0);

    $safeLen = strlen($safe);
    $userLen = strlen($user);

    // Set the result to the difference between the lengths
    $result = $safeLen - $userLen;

    // Note that we ALWAYS iterate over the user-supplied length
    // This is to prevent leaking length information
    for ($i = 0; $i < $userLen; $i++) {
        // Using % here is a trick to prevent notices
        // It's safe, since if the lengths are different
        // $result is already non-0
        $result |= (ord($safe[$i % $safeLen]) ^ ord($user[$i]));
    }

    // They are only identical strings if $result is exactly 0...
    return $result === 0;
}


// Response codes:
// 	0: success
// 	1: error - FB already linked with another account
// 	2: successfully linked FB with account
function checkFbUser($fbId, $fbFirstName, $fbLastName, $fbEmail, $userId) {

	$mysqli = getMysqli();
	$image = "//graph.facebook.com/$fbId/picture";
	$timestamp = date('Y-m-d H:i:s');

	if ($mysqli->query("SELECT * FROM users WHERE fb_id = '$fbId'")->num_rows !== 0) {
		// we have this user. just log him in, and update his info
		if ($userId) {
			$respCode = 1;
		} else {
			$updateQuery = "UPDATE users SET fb_fname='$fbFirstName', fb_lname='$fbLastName' WHERE fb_id='$fbId'";
			//$updateQuery = "UPDATE users SET fb_name='$fbFullName' WHERE fb_id='$fbId'";
			mysqli_query($mysqli, $updateQuery);
	
			$userId = mysqli_fetch_assoc($mysqli->query("SELECT id, username FROM users WHERE fb_id='$fbId'"));
			mysqli_query($mysqli, "INSERT INTO session_history VALUES ({$userId['id']}, 'fb_login', '$timestamp')");

			onLogin($userId['id']);
			if (strlen($userId['username']) > 0) {
				session_start();
				$_SESSION['user'] = $userId['username'];
			}
		}
	} else if ($userId) {
		// we have a user, but no facebook information. update it
		$updateQuery = "UPDATE users SET fb_id='$fbId', fb_fname='$fbFirstName', fb_lname='$fbLastName', image='$image'" . ((strlen($fbEmail) > 0) ? ", email='$fbEmail'" : "") . " WHERE id=$userId";
		mysqli_query($mysqli, $updateQuery);
		mysqli_query($mysqli, "INSERT INTO session_history VALUES ($userId, 'fb_login', '$timestamp')");
		$respCode = 2;
	} else {
		// we don't have this user. Add him to the DB, make him active
		$fbEmail = (strlen($fbEmail) > 0) ? "'$fbEmail'" : "NULL";
		$hash = md5( rand(0, 1000) );
		$createUserQuery = "INSERT INTO users VALUES (NULL, NULL, NULL, '$fbId', '$fbFirstName', '$fbLastName', $fbEmail, '$image', 0, 1, 1, '$hash', '', '', 1)";
		mysqli_query($mysqli, $createUserQuery);

		$userId = mysqli_fetch_assoc($mysqli->query("SELECT id FROM users WHERE fb_id='$fbId'"))['id'];
		mysqli_query($mysqli, "INSERT INTO session_history VALUES ($userId, 'fb_signup', '$timestamp')");
		//echo $createUserQuery;
	}
	return ($respCode) ? $respCode : 0;
}

function isLoggedIn($session) {
	return (isset($session['fbId']) || isset($session['user']));
}



function getPersonType($personId) {
	global $mysqli;
	$actor = false;
	$director = false;
	if ($mysqli->query("SELECT actor_id FROM roles WHERE actor_id = $personId")->num_rows !== 0) {
		$actor = true;
	}
	if ($mysqli->query("SELECT director FROM movies WHERE director = $personId")->num_rows !== 0) {
		$director = true;
	}
	if ($actor && $director)
		return 3;
	else if ($actor)
		return 1;
	else
		return 2;
}

function getData($endpoint) {
	$session = curl_init($endpoint);
	curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
	$data = curl_exec($session);
	curl_close($session);
	return json_decode($data);
}

// takes array of movie IDs, return short movie infos
function getMoviesShort($movieIds) {
	global $mysqli;

	$memcached = new Memcached();
	$memcached->addServer('localhost', 11211) or die ("Could not connect");

	$movies = array();

	foreach ($movieIds as $id) {
		$movie = $memcached->get("movie_short_$id") ?: $memcached->get("movie_$id");

		if ($movie) {
			$movies[] = $movie;
		} else {
			$movie = array();
			$query = "SELECT m.id,m.title,m.year,m.runtime,m.genres,m.adult,m.synopsis,m.tagline,m.director,m.img_link,m.language,m.mpaa,m.imdb_id,i.imdb_rating
						FROM movies m INNER JOIN imdb i ON m.id=i.id WHERE m.id=$id";
			if ($s = $mysqli->query($query)) {
				if ($r = mysqli_fetch_assoc($s)) {
					$movie['params'] = $r;
				}
			}
			$movie['actors'] = array();
			$castQuery = "SELECT r.actor_id, a.name, a.photo, r.character FROM roles r INNER JOIN actors a ON a.id = r.actor_id AND r.movie_id = $id ORDER BY r.star DESC";
			if ($s = $mysqli->query($castQuery)) {
				while ($c = mysqli_fetch_assoc($s)) {
					$movie['actors'][] = $c;
				}
			}
			$memcached->set("movie_short_$id", $movie);
			$movies[] = $movie;
		}
	}
	return $movies;	
}

// function addToAutocomplete($array) {
// 	$fh = fopen("../movies3.json", "a+") or die("can't open file");
// 	$stat = fstat($fh);
// 	ftruncate($fh, $stat['size']-1);
// 	$written = "," . json_encode($array) . "]";
// 	fwrite($fh, $written);
// 	fclose($fh);
// }

function gen_genres($genres) {
	$genreArr = explode(' | ', $genres);
	foreach($genreArr as &$g) {
		$g = "<a class='genre-link' href='genre/" . urlencode(strtolower($g)) . "'>$g</a>";
	}
	return implode(' | ', $genreArr);
}

function birth_and_death($dob, $dod) {
	$bd = "<i class='fa fa-calendar'></i> ";
	if ($dob != "0000-00-00") {
		$bd .= "Born ";
		$bd .= date('F j, Y', strtotime($dob)) . (((strlen($dod) < 1) || ($dod == "0000-00-00")) ? (" (age " . floor((time() - strtotime($dob)) / 31556926) . ")") : (" - Died " . date('F j, Y', strtotime($dod)) .
		" (age " . floor((strtotime($dod) - strtotime($dob)) / 31556926) . ")"));
	}
	return $bd;
}


function gen_time_ago($timebefore) {
	date_default_timezone_set("America/New_York");
	$timenow = time();
	$timebefore = strtotime($timebefore);
	$sb = $timenow - $timebefore;
	if ($sb < 60) {
		return "$sb second" . (($sb > 1)?"s":"") . " ago";
	} else if ($sb < 3600) {
		return floor($sb/60) . " minute" . ((floor($sb/60) > 1)?"s":"") . " ago";
	} else if ($sb < 86400) {
		return floor($sb/3600) . " hour" . ((floor($sb/3600) > 1)?"s":"") . " ago";
	} else if ($sb < 172800) {
		return "yesterday";
	} else if ($sb < 259200) {
		return "2 days ago";
	} else {
		return "on " . date('n/j/y', $timebefore);
	}
}

function gen_sql_search($searchterms) {
	$q = "SELECT * FROM movies WHERE title LIKE '%";
	$q = $q . implode("%' OR title LIKE '%", $searchterms) . "%' ORDER BY  `imdb_rating` DESC ";
	return $q;
}

function rewriteUrl($title) {
	$title = str_replace("'", " ", $title);
	$patterns = array('/^([^a-z A-Z0-9\-]+)/', '/([^a-z A-Z0-9\-]+)$/', '/[^a-z A-Z0-9\-]+/');
	$replacements = array('', '', ' ');
	$title = preg_replace($patterns, $replacements, $title);
	return strtolower(str_replace(" ", "-", $title));
}
    
// converts runtime from minutes to xhxx format
function gen_time($minutes) {
	if ($minutes < 60) return $minutes . ' min';
	else {
	$h = floor($minutes/60);
	$m = $minutes % 60;
	return $h . 'h' . (($m < 10) ? '0'.$m : $m);
	}
}

function format_revenue($rev) {
	if ($rev > 0)
		return '$' . number_format($rev);
	else
		return 'N/A';
}

// replaces instances of ? with '
function fix_aps($string) {
	$string = str_replace("?","'", $string);
	$string = preg_replace("/' ([A-Z])/","? $1", $string);
	if (substr($string, -1) == "'")
		$string = substr($string, 0, -1) . "?";
	return $string;
}

// add imdb rating
function add_imdb_rating($link, $imdb_id) {
	
	$json = file_get_contents("http://www.omdbapi.com/?i=$imdb_id");
	$obj = json_decode($json);
	$link['imdb_rating'] = $obj->imdbRating;
	return $link;
}

// get imdb rating
function get_imdb_rating($imdb_id) {
	$json = file_get_contents("http://www.omdbapi.com/?i=$imdb_id");
	$obj = json_decode($json);
	return $obj->imdbRating;
}

function get_language($imdb_id) {
	$json = file_get_contents("http://www.omdbapi.com/?i=$imdb_id");
	$obj = json_decode($json);
	$arr = explode(",", $obj->Language, 2);
	return mysql_escape_string($arr[0]);
}

// generate regex to find name and bold it in the string.
function find_and_bold($name, $string) {
	$name_arr = explode(' ', $name);
	$regex = "/";
	for ($i = 0; $i < sizeof($name_arr); $i++) {
	if ($i < sizeof($name_arr)-1) {
	$regex = $regex . $name_arr[$i] . " [a-zA-Z]+ ";
	} else {
	$regex = $regex . $name_arr[$i] . "/";
	}
	}
	$matches = array();
	preg_match($regex, $string, $matches);
	$string = str_replace($matches[0], '<b>' . $matches[0] . '</b>', $string);
	$string = str_replace($name, '<b>' . $name . '</b>', $string);
	$string = str_replace(', Jr.', '<b>, Jr.</b>', $string);
	
	return $string;
}

// get average luminance, by sampling $num_samples times in both x,y directions
function get_avg_luminance($filename, $num_samples=10) {
    $img = imagecreatefromjpeg($filename);
    $width = imagesx($img);
    $height = imagesy($img);
    $x_step = intval($width/$num_samples);
    $y_step = intval($height/$num_samples);
    $total_lum = 0;
    $sample_no = 1;
    for ($x=0; $x<$width; $x+=$x_step) {
        for ($y=0; $y<$height; $y+=$y_step) {
            $rgb = imagecolorat($img, $x, $y);
            $r = ($rgb >> 16) & 0xFF;
            $g = ($rgb >> 8) & 0xFF;
            $b = $rgb & 0xFF;
            // choose a simple luminance formula from here
            // http://stackoverflow.com/questions/596216/formula-to-determine-brightness-of-rgb-color
            $lum = ($r+$r+$b+$g+$g+$g)/6;
            $total_lum += $lum;
            // debugging code
 			// echo "$sample_no - XY: $x,$y = $r, $g, $b = $lum<br />";
            $sample_no++;
        }
    }
    // work out the average
    $avg_lum  = $total_lum/$sample_no;
    return $avg_lum;
}

// generate MySQL query given filter selections
// WHERE imdb_rating REGEXP '[0-9].[0-9]'
function filter_query($arr, $selector) {
	if ($arr['orderby'] == 'IMDb rating')
		$arr['orderby'] = 'imdb_rating';
	if (strpos($arr['mintime']) < 0)
		$arr['mintime'] = substr($arr['mintime'], 0, strpos($arr['mintime'], ' '));
	if (strpos($arr['maxtime']) < 0)
		$arr['maxtime'] = substr($arr['maxtime'], 0, strpos($arr['maxtime'], ' '));
	$arr['mintime'] = str_replace('min', '', $arr['mintime']);
	$arr['maxtime'] = str_replace('min+', '', $arr['maxtime']);
	$arr['maxtime'] = str_replace('min', '', $arr['maxtime']);
	$arr['maxtime'] = ($arr['maxtime'] == 200) ? '9999' : $arr['maxtime'];
	$arr['minyear'] = ($arr['minyear'] == 1950) ? '0' : $arr['minyear'];
	$query = "SELECT $selector FROM movies INNER JOIN imdb ON movies.id=imdb.id WHERE year >= " . $arr['minyear'];
	$query .= " AND year <= " . $arr['maxyear'];
	$query .= " AND runtime >= " . $arr['mintime'];
	$query .= " AND runtime <= " . $arr['maxtime'];
	$query .= (($arr['language'] == "Any language") ? ("") : (" AND language LIKE '" . $arr['language'] . "'"));
	
	$genre = "";
	if ($arr['genre'] != "Any") {
		$genre .= " AND (genres LIKE '%| " . $arr['genre'] . "'";
		$genre .= " OR genres LIKE '" . $arr['genre'] . " |%'";
		$genre .= " OR genres LIKE '%| " . $arr['genre'] . " |%'";
		$genre .= " OR genres LIKE '" . $arr['genre'] . "')";
	}
	$query .= $genre;
	if ($arr['mpaa'] != "Any") {
		$query .= " AND mpaa LIKE '" . $arr['mpaa'] . "'";	
	}
	if ($arr['orderby'] == 'imdb_rating')
		$query .= " AND imdb_rating REGEXP '^-?[0-9.]+$'";
	$query .= " ORDER BY " . $arr['orderby'] . " DESC";
	return $query;
}

// Regenerate filter/results url with params
function gen_url($arr) {
	$url = "year-min=" . $arr['minyear'] . "&year-max=" . $arr['maxyear'];
	$url .= "&runtime-min=" . urlencode($arr['mintime']) . "&runtime-max=" . urlencode($arr['maxtime']);
	$url .= "&language=" . $arr['language'] . "&genre=" . $arr['genre'] . "&mpaa=" . $arr['mpaa'] . "&orderby=" . $arr['orderby'];
	return $url;
}

// generates pagination. Takes 3 parameters:
// $current: the current page
// $numPerPage: the number of items per page
// $num_pages: the total number of pages
// $url: the url of pages
function gen_pagination ($current, $numPerPage, $num_pages, $url) {
	echo "<div class='pag-wrapper'><ul class='pagination'>";
	// PREVIOUS
	echo "<li". (($current > 1) ? "><a href='$url" . ($current - 1) . "'>" : " class='disabled'><a>") ."&laquo;</a></li> ";
	
	for ($p = max($current-$numPerPage,1); $p <= min($num_pages, $current+$numPerPage); $p++) {
		if (($p == $current-$numPerPage) && ($p > 1))
			echo "<li><a href='$url" . "1'>1</a></li><li class='disabled'><a href='#'>...</a></li> ";
		if ($p == $current)
			echo "<li class='active'><a>$p</a></li>";
		else
			echo "<li><a href='$url$p'>$p</a></li> ";
		if (($p == $current+$numPerPage) && ($p < $num_pages))
			echo "<li class='disabled'><a>...</a></li> <li><a href='$url$num_pages'>$num_pages</a></li> ";
	}
	// NEXT
	echo "<li". (($current < $num_pages) ? "><a href='$url" . ($current + 1) . "'>" : " class='disabled'><a>") ."&raquo;</a></li> ";
	echo "</ul></div>";
}

// generate the page header
function gen_page_header ($title, $added = '') {

	header('Content-Type: text/html; charset=utf-8');

	echo "<!DOCTYPE html>
		<html>
		<head>
		<meta name='viewport' content='width=device-width, initial-scale=1.0'>
		<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>
		<meta name='description' content=\"Want to watch a movie? Don't know where to find it? readyto.watch lets you search for your favorite movies and 
		tells you where you can watch them.\">
		<meta name='keywords' content='where, links, ready, to, watch, streaming, stream, movies, watch movies online, online, rent, buy, iTunes, Amazon, Netflix, Crackle'>
		<meta property='og:image' content='http://readyto.watch/images/logo_small.png' />
    	<link rel='image_src' href='http://readyto.watch/images/logo_square.png' />

    	<meta name='google-site-verification' content='5r7OZ2x9cXOMobkXpZrIbv48KrWMeRTI7g_4M8NmY8Q' />

		<title>$title</title>
		<base href='/readytowatch/'>
		<link type='text/css' rel='stylesheet' href='css/main.min.css' />
		<link rel='stylesheet' href='font-awesome/css/font-awesome.min.css'>
		<link rel='shortcut icon' href='images/favicon.ico' type='image/x-icon'>
		<link rel='icon' href='images/favicon.ico' type='image/x-icon'>
		<script>
  			(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  			(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  			m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  			})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
  			ga('create', 'UA-51952232-1', 'readyto.watch');
  			ga('send', 'pageview');
		</script>
		$added
		</head>";
}

function gen_page_footer ($addedScripts = '', $noFooter = false) {
	if (!$noFooter)
		include 'footer.php';
	echo "<script src='js/jquery-1.11.1.min.js'></script>
		<script src='js/jquery-ui.min.js'></script>
		<script src='js/bootstrap/bootstrap.min.js'></script>
		<script src='js/bootstrap/bootstrap3-typeahead-min.js'></script>
		<script src='js/custom.js'></script>
		$addedScripts

		</body>
		</html>";
	// echo "<script src='js/main.min.js'></script>
	// 	$addedScripts

	// 	</body>
	// 	</html>";
}

function get_ip()
{
    if($_SERVER){
        if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $adress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        elseif(isset($_SERVER['HTTP_CLIENT_IP']))
            $adress = $_SERVER['HTTP_CLIENT_IP'];
        else
            $adress = $_SERVER['REMOTE_ADDR'];
    } else {
        if(getenv('HTTP_X_FORWARDED_FOR'))
            $adress = getenv('HTTP_X_FORWARDED_FOR');
        elseif(getenv('HTTP_CLIENT_IP'))
            $adress = getenv('HTTP_CLIENT_IP');
        else
            $adress = getenv('REMOTE_ADDR');
    }

    return $adress;
}

?>