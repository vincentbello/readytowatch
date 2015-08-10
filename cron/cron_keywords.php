<?php

require_once __DIR__ . '/../includes/mysqli_connect.php';

$time_start = microtime(true); 

set_time_limit(86400);

header('Content-Type: text/html; charset=utf-8');
$mysqli->set_charset("utf8");

$count = 0;
$apikey = '2e44f5c2d522defe7f32d188e59fcaa8';
if ($i = $mysqli->query("SELECT id FROM movies ORDER BY popularity DESC")) {
	while ($mov = mysqli_fetch_assoc($i)) {
		$backdrop = "";
		$count++;
		$endpoint = "https://api.themoviedb.org/3/movie/".$mov['id']."/keywords?api_key=$apikey";
		$session = curl_init($endpoint);
		curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
		$data = curl_exec($session);
		curl_close($session);
		$m = json_decode($data);
		if ($m === NULL) {
			echo "Error parsing json, id " . $mov['id'];
			continue;
		}
		if ($m->keywords) {
			foreach ($m->keywords as $keyword) {
				mysqli_query($mysqli, "INSERT INTO keywords VALUES (" . $keyword->id . ", '" . $keyword->name . "')");
				mysqli_query($mysqli, "INSERT INTO movie_keywords VALUES (" . $mov['id'] . ", " . $keyword->id . ")");
			}
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