<?php

require_once(__DIR__ . '/../../includes/mysqli_connect.php');
require_once(__DIR__ . '/../../links/link_functions.php');
require_once(__DIR__ . '/functions.php');
ini_set('max_execution_time', 300);

if ($m = $mysqli->query("SELECT id, title, runtime, director FROM movies ORDER BY popularity DESC")) {
	while ($params = mysqli_fetch_assoc($m)) {
		$director = mysqli_fetch_assoc($mysqli->query("SELECT name FROM actors WHERE id=" . $params['director']));
		$link = get_itunes_link($params['title'], $params['runtime'], $director['name']);

		mysqli_query($mysqli, "DELETE FROM itunes WHERE id=" . $params['id']);
		$query = "INSERT INTO itunes VALUES (".$params['id'].",'".$link['link']."','".$link['rent']."','".$link['buy']."','".$link['itunesId']."','".date('Y-m-d H:i:s')."')";
		mysqli_query($mysqli, $query);

		if ($link['link']) {
			if ($a = $mysqli->query("SELECT username FROM alerts WHERE all=1 AND id=".$params['id'])) {
				while($u = mysqli_fetch_assoc($a)) {
					$user = mysqli_fetch_assoc($mysqli->query("SELECT email FROM users WHERE username='" . $u['username'] . "'"));
					send_new_link_email($user['email'], $u['username'], $params['id'], $params['title'], 'iTunes');
					mysqli_query($mysqli, "UPDATE alerts SET all=0, itunes=0 WHERE username='" . $u['username'] . "' AND id=" . $params['id']);
				}
			}
	
			if ($a = $mysqli->query("SELECT username FROM alerts WHERE itunes=1 AND id=".$params['id'])) {
				while($u = mysqli_fetch_assoc($a)) {
					$user = mysqli_fetch_assoc($mysqli->query("SELECT email FROM users WHERE username='" . $u['username'] . "'"));
					send_new_link_email($user['email'], $u['username'], $params['id'], $params['title'], 'iTunes');
					mysqli_query($mysqli, "UPDATE alerts SET itunes=0 WHERE username='" . $u['username'] . "' AND id=" . $params['id']);
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