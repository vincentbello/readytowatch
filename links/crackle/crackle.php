<?php

if (isset($_POST['id'])) {
	$id = $_POST['id'];

	require_once '../../includes/mysqli_connect.php';
	require_once '../link_functions.php';

	// LINKS
	$params = mysqli_fetch_assoc($mysqli->query("SELECT * FROM movies WHERE id=$id"));
		
	$q = "SELECT * FROM crackle WHERE id=$id";
	if ($l = $mysqli->query($q)) {
		if ($arr = mysqli_fetch_assoc($l)) {
			if (((time() - strtotime($arr['timestamp']))/86400) < 14) {
				// if we have this movie in the links table and have updated it in the last seven days
				$crackle = $arr['link'];
			} else {
				$crackle = get_crackle_link($params['title']);
				mysqli_query($mysqli, "UPDATE crackle SET link='$crackle',timestamp='" . date('Y-m-d H:i:s') . "' WHERE id=$id");
			}
		} else {
			$crackle = get_crackle_link($params['title']);
			mysqli_query($mysqli, "INSERT INTO crackle VALUES ($id,'$crackle','" . date('Y-m-d H:i:s') . "')");	
		}
			if (strlen($crackle) > 0) {
				echo '<div class="link"><a target="_blank" href="' .$crackle.'"><img data-toggle="tooltip" alt="Crackle" title="Crackle" class="link_icon" src="images/crackle_icon.png"></a><br>
				Stream: <b class="price">Free</b><br>(Ads)</div>';
			}
	}
}

?>