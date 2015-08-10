<?php

require_once __DIR__ . '/../includes/mysqli_connect.php';

$time_start = microtime(true); 

set_time_limit(86400);

$count = 0;
$apikey = '2e44f5c2d522defe7f32d188e59fcaa8';
if ($i = $mysqli->query("SELECT id FROM movies")) {
	while ($mov = mysqli_fetch_assoc($i)) {
		$count++;
		$endpoint4 = "https://api.themoviedb.org/3/movie/" . $mov['id'] . "/videos?api_key=$apikey";
		$session4 = curl_init($endpoint4);
		curl_setopt($session4, CURLOPT_RETURNTRANSFER, true);
		$data4 = curl_exec($session4);
		curl_close($session4);
		$videos = json_decode($data4);
		$trailer = '';
		if ($videos === NULL) {
			echo "Error parsing ratings, id $id";
		} else {
			foreach($videos->results as $res) {
				if (($res->site == 'YouTube') && ($res->type == 'Trailer')) {
					$trailer = $res->key;
					break;
				}
			}
		}
		mysqli_query($mysqli, "UPDATE movies SET trailer='$trailer' WHERE id=" . $mov['id']);
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