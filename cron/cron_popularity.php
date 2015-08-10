<?php

require_once __DIR__ . '/../includes/mysqli_connect.php';

function getData ( $endpoint ) {
	$session = curl_init($endpoint);
	curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
	$data = curl_exec($session);
	curl_close($session);
	$m = json_decode($data);	
	return $m;
}

$time_start = microtime(true); 

set_time_limit(86400);

$count = 0;
$apikey = '2e44f5c2d522defe7f32d188e59fcaa8';
if ($i = $mysqli->query("SELECT id FROM movies")) {
	while ($movie = mysqli_fetch_assoc($i)) {
		$count++;
		$m = getData("https://api.themoviedb.org/3/movie/{$movie['id']}?api_key=$apikey");

		if ($m === NULL) {
			echo "Error parsing json, id " . $movie['id'] . "\n";
			continue;
		}
		if (count($m) > 0) {
			if ($m->popularity) {
				$query = "UPDATE movies SET popularity=" . $m->popularity . " WHERE id={$movie['id']}";
				mysqli_query($mysqli, $query);
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