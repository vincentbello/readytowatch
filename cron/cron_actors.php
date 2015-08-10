<?php // update actors. Run every 6 months

$time_start = microtime(true); 

require_once(__DIR__ . '/../includes/mysqli_connect.php');
require_once(__DIR__ . '/../includes/functions.php');

header('Content-Type: text/html; charset=utf-8');
$mysqli->set_charset("utf8");

set_time_limit(86400);

$apikey = '2e44f5c2d522defe7f32d188e59fcaa8';
$count = 0;
$latest = json_decode(file_get_contents("https://api.themoviedb.org/3/person/latest?api_key=$apikey"))->id;

// 1200080
for ($id = 1; $id <= $latest; $id++) {

	include (__DIR__ . '/cron_get_actor_info.php');

	foreach($queryArr as $query) {
		mysqli_query($mysqli,$query);
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