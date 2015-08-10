<?php

require_once __DIR__ . '/../includes/mysqli_connect.php';
require_once __DIR__ . '/../includes/functions.php';

$time_start = microtime(true); 

set_time_limit(86400);

$count = 0;
if ($i = $mysqli->query("SELECT id, imdb_id FROM movies")) {
	while ($mov = mysqli_fetch_assoc($i)) {
		$count++;
		$imdb = get_imdb_rating($mov['imdb_id']);
		mysqli_query($mysqli, "DELETE FROM imdb WHERE id=" . $mov['id']);
		mysqli_query($mysqli, "INSERT INTO imdb VALUES (" . $mov['id'] . ",'$imdb','" . date('Y-m-d H:i:s') . "')");
	}
}

$mysqli->close();
$time_end = microtime(true);
$execution_time = ($time_end - $time_start);

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