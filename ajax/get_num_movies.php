<?php

require_once '../includes/functions.php';

require_once '../includes/mysqli_connect.php';

foreach ($_POST as &$g) {
  $g = urldecode($g);
}

$arr = array();

$arr = array('minyear' => $_POST['year_min'],
			'maxyear' => $_POST['year_max'],
			'mintime' => $_POST['runtime_min'],
			'maxtime' => $_POST['runtime_max'],
			'language' => $_POST['language'],
			'genre' => $_POST['genre'],
			'mpaa' => $_POST['mpaa'],
			'orderby' => $_POST['orderby']);

$sql = filter_query($arr, 'movies.id');
$num_movies = $mysqli->query($sql)->num_rows;
echo $num_movies;
$mysqli->close();
?>