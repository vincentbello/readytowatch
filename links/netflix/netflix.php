<?php

if (isset($_POST['id'])) {
	$id = $_POST['id'];

	require_once '../../includes/mysqli_connect.php';
	require_once '../link_functions.php';
	session_start();

	$netflixDisp = true;
	if (isset($_SESSION['user'])) {
		$account = mysqli_fetch_assoc($mysqli->query("SELECT netflix FROM users WHERE username='" . $_SESSION['user'] . "'"));
		if ($account['netflix'] == 0)
			$netflixDisp = false;
	}

	// LINKS
	$params = mysqli_fetch_assoc($mysqli->query("SELECT * FROM movies WHERE id=$id"));

	$q = "SELECT * FROM netflix WHERE id=$id";
	if ($l = $mysqli->query($q)) {
		if ($arr = mysqli_fetch_assoc($l)) {
			if (((time() - strtotime($arr['timestamp']))/86400) < 14) {
				// if we have this movie in the links table and have updated it in the last seven days
				$netflix = $arr['link'];
			} else {
				$netflix = get_netflix_link($params['title'], $params['year']);
				mysqli_query($mysqli, "UPDATE netflix SET link='$netflix',timestamp='" . date('Y-m-d H:i:s') . "' WHERE id=$id");
			}
		} else {
			$netflix = get_netflix_link($params['title'], $params['year']);
			mysqli_query($mysqli, "INSERT INTO netflix VALUES ($id,'$netflix','" . date('Y-m-d H:i:s') . "')");	
		}
			if ((strlen($netflix) > 0) && $netflixDisp) {
				echo '<div class="link"><a target="_blank" href="' . $netflix
				. '"><img alt="Netflix" data-toggle="tooltip" title="Netflix Instant Streaming" class="link_icon" src="images/netflix_icon.png"></a><br>
				Stream: <b class="price">Free</b></div>';
			}
	}
}

?>