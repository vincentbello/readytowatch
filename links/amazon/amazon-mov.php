<?php

header('Content-type: application/json');

if (isset($_POST['id'])) {
	$id = $_POST['id'];

	require_once '../../includes/mysqli_connect.php';
	require_once '../link_functions.php';

	session_start();

	$loggedIn = isset($_SESSION['user']) || isset($_SESSION['fbId']);

	$amazonDisp = true;
	if ($loggedIn) {
		$accountQuery = "SELECT amazon_prime FROM users WHERE " . (isset($_SESSION['user']) ? "username='{$_SESSION['user']}'" : "fb_id='{$_SESSION['fbId']}'");
		$account = mysqli_fetch_assoc($mysqli->query($accountQuery));
		if ($account['amazon_prime'] == 0)
			$amazonDisp = false;
	}
	
	// LINKS
	$params = mysqli_fetch_assoc($mysqli->query("SELECT * FROM movies WHERE id=$id"));
	$q = "SELECT * FROM amazon WHERE id=$id";
	if ($l = $mysqli->query($q)) {
		if ($arr = mysqli_fetch_assoc($l)) {
			$timestamp = $arr['timestamp'];
			if (((time() - strtotime($timestamp))/86400) < 14) {
				// if we have this movie in the links table and have updated it in the last seven days
				$amazon = ["link"=>$arr['link'], "rent"=>$arr['rent'], "buy"=>$arr['buy']];
			} else {
				$timestamp = date('Y-m-d H:i:s');
				$cast = array();
				if ($c = $mysqli->query("SELECT a.name FROM actors a INNER JOIN roles r ON a.id = r.actor_id AND r.movie_id = $id")) {
					while ($actor = mysqli_fetch_assoc($c)) {
						$cast[] = $actor['name'];
					}
				}
				$amazon = get_amazon_link($params['title'], $params['runtime'], implode('|', $cast));
				mysqli_query($mysqli, "UPDATE amazon SET link='" . $amazon['link'] . "',rent='" . $amazon['rent'] . "',buy='" . $amazon['buy'] . "',asin='".$amazon['asin']."',timestamp='$timestamp' WHERE id=$id");
			}
		} else {
			$cast = array();
			if ($c = $mysqli->query("SELECT a.name FROM actors a INNER JOIN roles r ON a.id = r.actor_id AND r.movie_id = $id")) {
				while ($actor = mysqli_fetch_assoc($c)) {
					$cast[] = $actor['name'];
				}
			}
			$timestamp = date('Y-m-d H:i:s');
			$amazon = get_amazon_link($params['title'], $params['runtime'], implode('|', $cast));
			mysqli_query($mysqli, "INSERT INTO amazon VALUES ($id,'" . $amazon['link'] . "','" . $amazon['rent'] . "','" . $amazon['buy'] . "','".$amazon['asin']."','$timestamp')");				
		}

		if ((strlen($amazon['link']) > 0) && $amazonDisp) {
			$text = '';
			$text .= "<span class='link-time'>" . gen_time($timestamp) . "</span>";
			$rent = explode("|", $amazon['rent']);
			$buy = explode("|", $amazon['buy']);
			if($rent[0]) $text .= "Rent: <b class='price'>$" . $rent[0] . "</b><br>";
			if($rent[1]) $text .= "Rent<sup>HD</sup>: <b class='price'>$" . $rent[1] . "</b><br>";
			if($buy[0]) $text .= "Buy: <b class='price'>$" . $buy[0] . "</b><br>";
			if($buy[1]) $text .= "Buy<sup>HD</sup>: <b class='price'>$" . $buy[1] . "</b><br>";
			if(strlen($amazon['rent'].$amazon['buy']) == 0) $text .= "<i class='fa fa-external-link-square'></i> <a target='_blank' href='" . $amazon['link'] . "'>Follow link for prices</a>.";
			

			$response = array(
				'link' => (string)$amazon['link'],
				'text' => $text
			);
			echo json_encode($response);
		}

		if (strlen($amazon['link']) == 0) {
			$available = '';
			if (isset($_SESSION['user']) && ($mysqli->query("SELECT * FROM alerts WHERE id=$id AND amazon=1 AND username='".$_SESSION['user']."'")->num_rows > 0))
				$available = "We'll email you when this movie is available on Amazon.";
			else
				$available = "Want us to <a class='alert-me'><b>email you</b></a> when it becomes available?";
			echo json_encode(array('link' => '', 'text' => 'Sorry, we don\'t have an Amazon link.<br>' . $available));
		}

	}
}

?>