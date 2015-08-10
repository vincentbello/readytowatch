<?php

require_once __DIR__ . '/../includes/mysqli_connect.php';

$time_start = microtime(true); 

set_time_limit(86400);

header('Content-Type: text/html; charset=utf-8');
$mysqli->set_charset("utf8");

$count = 0;
$apikey = '2e44f5c2d522defe7f32d188e59fcaa8';
if ($i = $mysqli->query("SELECT id,genres FROM movies")) {
	while ($mov = mysqli_fetch_assoc($i)) {
		$genres = explode(' | ', $mov['genres']);
		foreach($genres as $genre) {
			$query = $mysqli->query("SELECT keyword_id FROM keywords WHERE keyword LIKE '$genre'");
			if ($query->num_rows > 0)
				$kId = mysqli_fetch_assoc($query)['keyword_id'];
			else
				$kId = substr(number_format(time() * mt_rand(),0,'',''),0,8);
			mysqli_query($mysqli, "INSERT INTO keywords VALUES ($kId, '$genre')");
			mysqli_query($mysqli, "INSERT INTO movie_keywords VALUES (" . $mov['id'] . ", $kId)");
		}
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
if ($count) {
	echo "\n" . $count . " requests in " . $execution_time . " seconds means 1 request in " . ($execution_time/$count) .
	" seconds.";
}
echo "\n\n---------------------------\n";




?>