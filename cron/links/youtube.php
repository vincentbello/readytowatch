<?php

require_once(__DIR__ . '/../../includes/mysqli_connect.php');
require_once(__DIR__ . '/../../links/link_functions.php');
require_once(__DIR__ . '/functions.php');
ini_set('max_execution_time', 300);

if ($m = $mysqli->query("SELECT id, title FROM movies ORDER BY popularity DESC")) {
	while ($params = mysqli_fetch_assoc($m)) {
		$cast = array();
		if ($c = $mysqli->query("SELECT a.name FROM actors a INNER JOIN roles r ON a.id = r.actor_id AND r.movie_id = {$params['id']}")) {
			while ($actor = mysqli_fetch_assoc($c)) {
				$cast[] = $actor['name'];
			}
		}
		$link = get_youtube_link($params['title'], $cast);

		mysqli_query($mysqli, "DELETE FROM youtube WHERE id=" . $params['id']);
		$query = "INSERT INTO youtube VALUES (".$params['id'].",'".$link."','".date('Y-m-d H:i:s')."')";
		mysqli_query($mysqli, $query);

		if ($link) {
			if ($a = $mysqli->query("SELECT username FROM alerts WHERE all=1 AND id=".$params['id'])) {
				while($u = mysqli_fetch_assoc($a)) {
					$user = mysqli_fetch_assoc($mysqli->query("SELECT email FROM users WHERE username='" . $u['username'] . "'"));
					send_new_link_email($user['email'], $u['username'], $params['id'], $params['title'], 'YouTube');
					mysqli_query($mysqli, "UPDATE alerts SET all=0, youtube=0 WHERE username='" . $u['username'] . "' AND id=" . $params['id']);
				}
			}
	
			if ($a = $mysqli->query("SELECT username FROM alerts WHERE youtube=1 AND id=".$params['id'])) {
				while($u = mysqli_fetch_assoc($a)) {
					$user = mysqli_fetch_assoc($mysqli->query("SELECT email FROM users WHERE username='" . $u['username'] . "'"));
					send_new_link_email($user['email'], $u['username'], $params['id'], $params['title'], 'YouTube');
					mysqli_query($mysqli, "UPDATE alerts SET youtube=0 WHERE username='" . $u['username'] . "' AND id=" . $params['id']);
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