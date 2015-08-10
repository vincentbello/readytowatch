<?php

require_once(__DIR__ . '/../includes/mysqli_connect.php');

$count = 0;

if($allIds = $mysqli->query("SELECT id FROM movies")) {
	while($mov = mysqli_fetch_assoc($allIds)) {
    $count++;
		$query = "SELECT mvs.id
          FROM movie_keywords mvs
          JOIN movie_keywords mvs2
          ON mvs2.id = {$mov['id']} 
          AND mvs.keyword_id = mvs2.keyword_id
          JOIN movies m
          ON m.id = mvs.id
          GROUP BY mvs.id
          HAVING COUNT(*) >= 3
          ORDER BY COUNT(*) DESC
          LIMIT 18";

        if($relatedMovs = $mysqli->query($query)) {
        	while($relatedMov = mysqli_fetch_assoc($relatedMovs)) {
        		mysqli_query($mysqli, "INSERT INTO related VALUES ({$mov['id']}, {$relatedMov['id']})");
        	}
        }
	}
}

mysqli_query($mysqli, "DELETE FROM related WHERE id = related_id");

if ($i = $mysqli->query("SELECT * FROM related")) {
  while ($mov = mysqli_fetch_assoc($i)) {
    mysqli_query($mysqli, "INSERT INTO related VALUES({$mov['related_id']}, {$mov['id']})");
  }
}

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