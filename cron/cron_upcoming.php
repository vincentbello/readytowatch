<?php // every week. get upcoming titles

// AAAAH utf8_decode(utf8_decode(

$time_start = microtime(true); 

require_once(__DIR__ . '/../includes/mysqli_connect.php');
require_once(__DIR__ . '/../includes/functions.php');

header('Content-Type: text/html; charset=utf-8');
$mysqli->set_charset("utf8");

set_time_limit(36000);
$apikey = '2e44f5c2d522defe7f32d188e59fcaa8';
$count = 0;

$u = getData("https://api.themoviedb.org/3/movie/upcoming?api_key=$apikey");

if ($u === NULL) {
	echo "Error parsing upcoming";
} else {
	$ids = array();
	foreach($u->results as $upcoming) {
		$ids[] = $upcoming->id;
	}

	foreach($ids as $id) {
		$count++;
		include __DIR__ . "/cron_get_movie_info.php";
		if (count($queryArr) > 0) {
			foreach($queryArr as $query) {
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