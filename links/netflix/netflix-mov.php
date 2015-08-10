<?php

header('Content-type: application/json');

if (isset($_POST['id'])) {
	$id = $_POST['id'];

	require_once '../../includes/mysqli_connect.php';
	require_once '../link_functions.php';

	session_start();

	$loggedIn = (isset($_SESSION['user']) || isset($_SESSION['fbId']));

	$netflixDisp = true;
	if ($loggedIn) {
		$accountQuery = "SELECT netflix FROM users WHERE " . (isset($_SESSION['user']) ? "username='{$_SESSION['user']}'" : "fb_id='{$_SESSION['fbId']}'");
		$account = mysqli_fetch_assoc($mysqli->query($accountQuery));
		if ($account['netflix'] == 0)
			$netflixDisp = false;
	}

	// LINKS
	$params = mysqli_fetch_assoc($mysqli->query("SELECT * FROM movies WHERE id=$id"));

	$q = "SELECT * FROM netflix WHERE id=$id";
	if ($l = $mysqli->query($q)) {
		if ($arr = mysqli_fetch_assoc($l)) {
			$timestamp = $arr['timestamp'];
			if (((time() - strtotime($timestamp))/86400) < 14) {
				// if we have this movie in the links table and have updated it in the last seven days
				$netflix = $arr['link'];
			} else {
				$timestamp = date('Y-m-d H:i:s');
				$netflix = get_netflix_link($params['title'], $params['year']);
				mysqli_query($mysqli, "UPDATE netflix SET link='$netflix',timestamp='$timestamp' WHERE id=$id");
			}
		} else {
			$timestamp = date('Y-m-d H:i:s');
			$netflix = get_netflix_link($params['title'], $params['year']);
			mysqli_query($mysqli, "INSERT INTO netflix VALUES ($id,'$netflix','$timestamp')");	
		}
		if ((strlen($netflix) > 0) && $netflixDisp) {
			$text = "<span class='link-time'>" . gen_time($timestamp) . "</span>";
			$text .= 'Stream: <b class="price">Free</b>';
			$response = array(
				'link' => $netflix,
				'text' => $text
			);
			echo json_encode($response);
		}
		if ((strlen($netflix) == 0) && $netflixDisp) {
			$available = '';
			if (isset($_SESSION['user']) && ($mysqli->query("SELECT * FROM alerts WHERE id=$id AND netflix=1 AND username='".$_SESSION['user']."'")->num_rows > 0))
				$available = "We'll email you when this movie is available on Netflix.";
			else
				$available = "Want us to <a class='alert-me'><b>email you</b></a> when it becomes available?";

			$text = "Sorry, we don't have a Netflix link.<br>$available";
			$response = array('link' => '', 'text' => $text);
			echo json_encode($response);
		}
	}
}

?>