<?php

require_once(__DIR__ . '/../../includes/mysqli_connect.php');
require_once(__DIR__ . '/../../links/link_functions.php');
require_once(__DIR__ . '/functions.php');
ini_set('max_execution_time', 300);

if ($m = $mysqli->query("SELECT id, title, year FROM movies ORDER BY popularity DESC")) {
	while ($params = mysqli_fetch_assoc($m)) {
		$link = get_netflix_link($params['title'], $params['year']);

		mysqli_query($mysqli, "DELETE FROM netflix WHERE id=" . $params['id']);
		$query = "INSERT INTO netflix VALUES (".$params['id'].",'".$link."','".date('Y-m-d H:i:s')."')";
		mysqli_query($mysqli, $query);

		if ($link) {
			if ($a = $mysqli->query("SELECT username FROM alerts WHERE all=1 AND id=".$params['id'])) {
				while($u = mysqli_fetch_assoc($a)) {
					$user = mysqli_fetch_assoc($mysqli->query("SELECT email FROM users WHERE username='" . $u['username'] . "'"));
					send_new_link_email($user['email'], $u['username'], $params['id'], $params['title'], 'Netflix');
					mysqli_query($mysqli, "UPDATE alerts SET all=0, netflix=0 WHERE username='" . $u['username'] . "' AND id=" . $params['id']);
				}
			}
	
			if ($a = $mysqli->query("SELECT username FROM alerts WHERE netflix=1 AND id=".$params['id'])) {
				while($u = mysqli_fetch_assoc($a)) {
					$user = mysqli_fetch_assoc($mysqli->query("SELECT email FROM users WHERE username='" . $u['username'] . "'"));
					send_new_link_email($user['email'], $u['username'], $params['id'], $params['title'], 'Netflix');
					mysqli_query($mysqli, "UPDATE alerts SET netflix=0 WHERE username='" . $u['username'] . "' AND id=" . $params['id']);
				}
			}
		}
	}
}

// REPORTING
echo "\n---------------------------\n\n";
echo "Finished executing " . __FILE__ . " at " . date('g:i:s A') . " on " . date('F j, Y') . ".\n";
echo "\n\n---------------------------\n";


?>