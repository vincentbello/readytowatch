<?php

if (isset($_POST['id'])) {
	$id = $_POST['id'];

	require_once '../../includes/mysqli_connect.php';
	require_once '../link_functions.php';

	// LINKS
	$params = mysqli_fetch_assoc($mysqli->query("SELECT * FROM movies WHERE id=$id"));

	$q = "SELECT * FROM youtube WHERE id=$id";
	if ($l = $mysqli->query($q)) {
		if ($arr = mysqli_fetch_assoc($l)) {
			if (((time() - strtotime($arr['timestamp']))/86400) < 14) {
				// if we have this movie in the links table and have updated it in the last seven days
				$youtube = $arr['videoId'];
			} else {
				$cast = array();
				if ($c = $mysqli->query("SELECT a.name FROM actors a INNER JOIN roles r ON a.id = r.actor_id AND r.movie_id = $id")) {
					while ($actor = mysqli_fetch_assoc($c)) {
						$cast[] = $actor['name'];
					}
				}
				$youtube = get_youtube_link($params['title'], $cast);
				mysqli_query($mysqli, "UPDATE youtube SET videoId='$youtube',timestamp='" . date('Y-m-d H:i:s') . "' WHERE id=$id");
			}
		} else {
			$cast = array();
			if ($c = $mysqli->query("SELECT a.name FROM actors a INNER JOIN roles r ON a.id = r.actor_id AND r.movie_id = $id")) {
				while ($actor = mysqli_fetch_assoc($c)) {
					$cast[] = $actor['name'];
				}
			}
			$youtube = get_youtube_link($params['title'], $cast);
			mysqli_query($mysqli, "INSERT INTO youtube VALUES ($id,'$youtube','" . date('Y-m-d H:i:s') . "')");	
		}
			if (strlen($youtube) > 0) {
				echo '<div class="link"><a target="_blank" href="http://www.youtube.com/watch?v=' . $youtube
				. '"><img alt="YouTube" data-toggle="tooltip" title="YouTube Movies" class="link_icon" src="images/youtube_icon.png"></a><br>
				<a target="_blank" href="http://www.youtube.com/watch?v='. $youtube. '">Follow link for prices</a></div>';
			}
	}
}

?>