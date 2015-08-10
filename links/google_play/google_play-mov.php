<?php

header('Content-type: application/json');

if (isset($_POST['id'])) {
	$id = $_POST['id'];

	require_once '../../includes/mysqli_connect.php';
	require_once '../link_functions.php';
	
	session_start();

	// LINKS
	$params = mysqli_fetch_assoc($mysqli->query("SELECT * FROM movies WHERE id=$id"));
	$q = "SELECT * FROM google_play WHERE id=$id";
	if ($l = $mysqli->query($q)) {
		if ($arr = mysqli_fetch_assoc($l)) {
			$timestamp = $arr['timestamp'];
			if (((time() - strtotime($timestamp))/86400) < 14) {
				// if we have this movie in the links table and have updated it in the last seven days
				$google_play = ["link"=>$arr['link'], "rent"=>$arr['rent'], "buy"=>$arr['buy']];
			} else {
				if (verify_google_play($arr['googleplay_id'])) {
					$google_play = ["link"=>$arr['link'], "rent"=>$arr['rent'], "buy"=>$arr['buy']];
					mysqli_query($mysqli, "UPDATE google_play SET timestamp='" . date('Y-m-d H:i:s') . "' WHERE id=$id");
				} else {
					$timestamp = date('Y-m-d H:i:s');
					$google_play = ["link"=>"", "rent"=>"", "buy"=>""];
					mysqli_query($mysqli, "UPDATE google_play SET link='',rent='',buy='',timestamp='$timestamp' WHERE id=$id");
				}
			}
		} else {
			$timestamp = date('Y-m-d H:i:s');
			$google_play = ["link"=>"", "rent"=>"", "buy"=>""];
			mysqli_query($mysqli, "INSERT INTO google_play VALUES ($id, '', '', '','', '$timestamp')");		
		}
		if (strlen($google_play['link']) > 0) {
			$text = '';
			$text .= "<span class='link-time'>" . gen_time($timestamp) . "</span>";
			$rent = explode("|", $google_play['rent']);
			$buy = explode("|", $google_play['buy']);
			if($rent[0]) $text .= "Rent: <b class='price'>$" . $rent[0] . "</b><br>";
			if($rent[1]) $text .= "Rent<sup>HD</sup>: <b class='price'>$" . $rent[1] . "</b><br>";
			if($buy[0]) $text .= "Buy: <b class='price'>$" . $buy[0] . "</b><br>";
			if($buy[1]) $text .= "Buy<sup>HD</sup>: <b class='price'>$" . $buy[1] . "</b><br>";
			if(strlen($google_play['rent'].$google_play['buy']) == 0) $text .= "<i class='fa fa-external-link-square'></i> <a href='" . $google_play['link'] . "'>Follow link for prices</a>.";
			

			$response = array(
				'link' => (string)$google_play['link'],
				'text' => $text
			);
			echo json_encode($response);
		}
		if (strlen($google_play['link']) == 0) {
			$available = '';
			if (isset($_SESSION['user']) && ($mysqli->query("SELECT * FROM alerts WHERE id=$id AND google_play=1 AND username='".$_SESSION['user']."'")->num_rows > 0))
				$available = "We'll email you when this movie is available on the Google Play Store.";
			else
				$available = "Want us to <a class='alert-me'><b>email you</b></a> when it becomes available?";
			echo json_encode(array('link' => '', 'text' => 'Sorry, we don\'t have a Google Play Store link.<br>' . $available));
		}

	}
}

?>