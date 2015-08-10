<?php

header('Content-type: application/json');

if (isset($_POST['id'])) {
	$id = $_POST['id'];

	require_once '../../includes/mysqli_connect.php';
	require_once '../link_functions.php';
	session_start();

	// LINKS
	$params = mysqli_fetch_assoc($mysqli->query("SELECT * FROM movies WHERE id=$id"));

	$q = "SELECT * FROM youtube WHERE id=$id";
	if ($l = $mysqli->query($q)) {
		if ($arr = mysqli_fetch_assoc($l)) {
			$timestamp = $arr['timestamp'];
			if (((time() - strtotime($timestamp))/86400) < 14) {
				// if we have this movie in the links table and have updated it in the last seven days
				$youtube = $arr['videoId'];
			} else {
				$cast = array();
				if ($c = $mysqli->query("SELECT a.name FROM actors a INNER JOIN roles r ON a.id = r.actor_id AND r.movie_id = $id")) {
					while ($actor = mysqli_fetch_assoc($c)) {
						$cast[] = $actor['name'];
					}
				}
				$timestamp = date('Y-m-d H:i:s');
				$youtube = get_youtube_link($params['title'], $cast);
				mysqli_query($mysqli, "UPDATE youtube SET videoId='$youtube',timestamp='$timestamp' WHERE id=$id");
			}
		} else {
			$cast = array();
			if ($c = $mysqli->query("SELECT a.name FROM actors a INNER JOIN roles r ON a.id = r.actor_id AND r.movie_id = $id")) {
				while ($actor = mysqli_fetch_assoc($c)) {
					$cast[] = $actor['name'];
				}
			}
			$timestamp = date('Y-m-d H:i:s');
			$youtube = get_youtube_link($params['title'], $cast);
			mysqli_query($mysqli, "INSERT INTO youtube VALUES ($id,'$youtube','$timestamp')");	
		}
		if (strlen($youtube) > 0) {
			$text = "<span class='link-time'>" . gen_time($timestamp) . "</span>";
			$text .= '<i class="fa fa-external-link-square"></i> <a target="_blank" href="http://www.youtube.com/watch?v='.$youtube.'">Follow link for prices</a>.';
			$response = array(
				'link' => "http://www.youtube.com/watch?v=$youtube",
				'text' => $text
			);
			echo json_encode($response);
		}
		if (strlen($youtube) == 0) {
			$available = '';
			if (isset($_SESSION['user']) && ($mysqli->query("SELECT * FROM alerts WHERE id=$id AND youtube=1 AND username='".$_SESSION['user']."'")->num_rows > 0))
				$available = "We'll email you when this movie is available on YouTube.";
			else
				$available = "Want us to <a class='alert-me'><b>email you</b></a> when it becomes available?";

			$text = "Sorry, we don't have a YouTube link.<br>$available";
			$response = array('link' => '', 'text' => $text);
			echo json_encode($response);
		}
	}
}

?>