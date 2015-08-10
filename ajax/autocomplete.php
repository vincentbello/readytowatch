<?php // autocomplete.php
include '../includes/mysqli_connect.php';
session_start();
$q = strip_tags($_POST['query']);
$return = array();

// $adult = " AND adult=0 AND genres NOT LIKE '%Erotic%'";
// if (isset ($_SESSION['user'])) {
// 	$account = mysqli_fetch_assoc($mysqli->query("SELECT * FROM users WHERE username='" . $_SESSION['user'] . "'"));
// 	if ($account['adult'] == 1)
// 		$adult = "";
// }

// $query1 = "SELECT id,title,year,img_link FROM movies WHERE title LIKE '$q%' OR title LIKE '% $q%'$adult ORDER BY popularity DESC LIMIT 2";
// //$query1 = "(SELECT id,title,year,img_link FROM movies WHERE title='$q'$adult LIMIT 2) UNION (SELECT id,title,year,img_link FROM movies WHERE title LIKE '%$q%'$adult LIMIT 3)";
// $query2 = "SELECT id,name,photo FROM actors WHERE name LIKE '%$q%' LIMIT 2";
// $t1 = $mysqli->query($query1);
// $t2 = $mysqli->query($query2);

// $movCount = $mysqli->query("SELECT id FROM movies WHERE title LIKE '$q%' OR title LIKE '% $q%'$adult")->num_rows;
// $actorCount = $mysqli->query("SELECT id FROM actors WHERE name LIKE '%$q%'")->num_rows;
// $return = array("<div class='header-detail'>Movies ($movCount)" . (($movCount) ? " <i class='fa fa-caret-down'></i></div>" : "</div>"));

// if($t1->num_rows > 0) {
// 	while($r1 = mysqli_fetch_assoc($t1)) {
// 		$return[] = '<img data-goto="movie/'.$r1['id'].'" src="' . (($r1['img_link'] == '') ? 'images/no_image_found.png' : 'http://image.tmdb.org/t/p/w185' . $r1['img_link']) .
// 		'" height="60"> ' . $r1['title'] . ((strlen($r1['year']) > 0) ? (" (" . $r1['year'] . ")") : "");
// 	}
// 	$t1->close();
// }

// $return[] = "<div class='header-detail'>People ($actorCount)" . (($actorCount) ? " <i class='fa fa-caret-down'></i></div>" : "</div>");

// if($t2->num_rows > 0) {
// 	while($r2 = mysqli_fetch_assoc($t2)) {
// 		$return[] = '<img data-goto="actor/'.$r2['id'].'" height="60" src="' . (($r2['photo'] == '') ? 'images/no_image_found.png' : 'http://image.tmdb.org/t/p/w185/' . $r2['photo']) .
// 		'"> ' . $r2['name'];
// 	}
// 	$t2->close();
// }

$query = "SELECT id,title,year,img_link FROM movies WHERE title LIKE '% $q%' OR title LIKE '$q%' ORDER BY popularity DESC LIMIT 4";
if ($q1 = $mysqli->query($query)) {
	while ($r1 = mysqli_fetch_assoc($q1)) {
		$return[] = $r1;
	}
}

$mysqli->close();
$json = json_encode($return);
echo $json;

// function rewriteUrl($title) {
// 	$title = str_replace("'", " ", $title);
// 	$patterns = ['/^([^a-z A-Z0-9\-]+)/', '/([^a-z A-Z0-9\-]+)$/', '/[^a-z A-Z0-9\-]+/'];
// 	$replacements = ['', '', ' '];
// 	$title = preg_replace($patterns, $replacements, $title);
// 	return strtolower(str_replace(" ", "-", $title));
// }
?>