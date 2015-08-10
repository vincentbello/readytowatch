<?php

require_once(__DIR__ . '/../includes/mysqli_connect.php');

$time_start = microtime(true); 

set_time_limit(86400);

header('Content-Type: text/html; charset=utf-8');
$mysqli->set_charset("utf8");

$count = 0;

//mysqli_query($mysqli, "TRUNCATE TABLE roles");

if($allIds = $mysqli->query("SELECT id,actor_id,chars FROM movies")) {
	while($mov = mysqli_fetch_assoc($allIds)) {
    $count++;
    $actor_ids = explode('|', $mov['actor_id']);
    $characters = explode('|', $mov['chars']);
    foreach ($actor_ids as $key => $actor) {
      $char = (isset($characters[$key])) ? ($characters[$key]) : '';
      $star = ($key <= 2) ? (3 - $key) : 0;
      $query = "INSERT INTO roles VALUES ($actor, {$mov['id']}, '$char', $star)";
      mysqli_query($mysqli, $query);
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