<?php

header('Content-type: application/json');

if (isset($_POST['id'])) {
	$id = $_POST['id'];

	require_once '../../includes/mysqli_connect.php';
	require_once '../link_functions.php';

	// LINKS
	$params = mysqli_fetch_assoc($mysqli->query("SELECT * FROM movies WHERE id=$id"));
		
	$q = "SELECT * FROM crackle WHERE id=$id";
	if ($l = $mysqli->query($q)) {
		if ($arr = mysqli_fetch_assoc($l)) {
			$timestamp = $arr['timestamp'];
			if (((time() - strtotime($timestamp))/86400) < 14) {
				// if we have this movie in the links table and have updated it in the last seven days
				$crackle = $arr['link'];
			} else {
				$timestamp = date('Y-m-d H:i:s');
				$crackle = get_crackle_link($params['title']);
				mysqli_query($mysqli, "UPDATE crackle SET link='$crackle',timestamp='$timestamp' WHERE id=$id");
			}
		} else {
			$timestamp = date('Y-m-d H:i:s');
			$crackle = get_crackle_link($params['title']);
			mysqli_query($mysqli, "INSERT INTO crackle VALUES ($id,'$crackle','$timestamp')");	
		}
		if (strlen($crackle) > 0) {
			$text = "<span class='link-time'>" . gen_time($timestamp) . "</span>";
			$text .= 'Stream: <b class="price">Free</b><br>(Ads)';
			$response = array(
				'link' => $crackle,
				'text' => $text
			);
			echo json_encode($response);
		} else {
			$available = '';
			if (isset($_SESSION['user']) && ($mysqli->query("SELECT * FROM alerts WHERE id=$id AND crackle=1 AND username='".$_SESSION['user']."'")->num_rows > 0))
				$available = "We'll email you when this movie is available on Crackle.";
			else
				$available = "Want us to <a class='alert-me'><b>email you</b></a> when it becomes available?";

			echo json_encode(array('link' => '', 'text' => 'Sorry, we don\'t have a Crackle link.<br>' . $available));
		}
	}
}

?>