<?php // update the cache. movie_$id and movie_short_$id

$time_start = microtime(true);

require_once(__DIR__ . '/../includes/mysqli_connect.php');
require_once(__DIR__ . '/../includes/functions.php');

header('Content-Type: text/html; charset=utf-8');

set_time_limit(86400);

$count1 = 0;
$count2 = 0;
$latest = json_decode(file_get_contents("https://api.themoviedb.org/3/movie/latest?api_key=2e44f5c2d522defe7f32d188e59fcaa8"))->id;
//248087

$memcached = new Memcached();
$memcached->addServer('localhost', 11211) or die ("Could not connect");

for ($id = 1; $id <= $latest; $id++) {

	if ($movieShort = $memcached->get("movie_short_$id")) {
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
		$memcached->replace("movie_short_$id", $movie);
		$count1++;
	}

	if ($movieLong = $memcached->get("movie_$id")) {
  		$movie = array();

  		$query1 = "SELECT * FROM movies INNER JOIN imdb ON movies.id=imdb.id WHERE movies.id=$id";
  		if($t = $mysqli->query($query1)) {
    		if ($result = mysqli_fetch_assoc($t)) {
    		  $movie['params'] = $result;
    		}
 		}

  		$movie['actors'] = array();
  		$starringQuery = "SELECT roles.actor_id, roles.character, actors.name, actors.photo
                    		FROM roles
                    		INNER JOIN actors
                    		ON roles.actor_id = actors.id
                    		AND roles.movie_id = $id
                    		ORDER BY roles.star DESC";
  		if($s = $mysqli->query($starringQuery)) {
    		while ($actor = mysqli_fetch_assoc($s)) {
    		  $movie['actors'][] = $actor;
    		}
  		}

  		if ($movie['params']['director']) {
    		if ($d = $mysqli->query("SELECT photo, name FROM actors WHERE id={$movie['params']['director']}")) {
      			if ($director = mysqli_fetch_assoc($d))
        			$movie['director'] = $director;
    		}
  		}

  		$movie['keywords'] = array();
  		if ($kq = $mysqli->query("SELECT k.keyword, k.keyword_id FROM keywords k INNER JOIN movie_keywords m ON k.keyword_id = m.keyword_id WHERE m.id=$id")) {
    		while ($kw = mysqli_fetch_assoc($kq)) {
      			$movie['keywords'][] = $kw;
    		}
  		}

  		$memcached->replace("movie_$id", $movie);
  		$count2++;
	}

}


$mysqli->close();
$time_end = microtime(true);
$execution_time = ($time_end - $time_start);
//execution time of the script

// REPORTING
echo "\n---------------------------\n\n";
echo "Finished executing " . __FILE__ . " at " . date('g:i:s A') . " on " . date('F j, Y') . ".\n";
echo "Total Execution Time: " . $execution_time/60 . " minutes (" . $execution_time/3600 . " hours)";
echo "\nThere are $count1 movies in the short movie cache, and $count2 movies in the long movie cache.\n";
if ($count) {
  echo "\n" . $count . " requests in " . $execution_time . " seconds means 1 request in " . ($execution_time/$count) .
  " seconds.";
}
echo "\n\n---------------------------\n";

?>