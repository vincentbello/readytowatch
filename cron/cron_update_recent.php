<?php // update movies that came out in the last 2 weeks/are coming out in the next 2 weeks. Run every 2 weeks

$time_start = microtime(true); 

require_once(__DIR__ . '/../includes/mysqli_connect.php');
require_once(__DIR__ . '/../includes/functions.php');

header('Content-Type: text/html; charset=utf-8');
$mysqli->set_charset("utf8");

set_time_limit(86400);
$apikey = '2e44f5c2d522defe7f32d188e59fcaa8';
$count = 0;

//248087
$query = "SELECT id FROM movies WHERE DATEDIFF( DATE( NOW( ) ) , movies.release_date ) < 14 AND DATEDIFF( DATE( NOW( ) ) , movies.release_date ) > -14";
if ($t = $mysqli->query($query)) {
	while($movie = mysqli_fetch_assoc($t)) {
		$id = $movie['id'];
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