<?php

header('Content-type: application/json');

if (isset($_POST['id'])) {
	$id = $_POST['id'];

	require_once '../../includes/mysqli_connect.php';
	require_once '../link_functions.php';
	
	// LINKS
	$params = mysqli_fetch_assoc($mysqli->query("SELECT * FROM movies WHERE id=$id"));
	$director = mysqli_fetch_assoc($mysqli->query("SELECT name FROM actors WHERE id=" . $params['director']));

	$q = "SELECT * FROM itunes WHERE id=$id";
	if ($l = $mysqli->query($q)) {
		if ($arr = mysqli_fetch_assoc($l)) {
			$timestamp = $arr['timestamp'];
			if (((time() - strtotime($timestamp))/86400) < 14) {
				// if we have this movie in the links table and have updated it in the last seven days
				$itunes = ["link"=>$arr['link'], "rent"=>$arr['rent'], "buy"=>$arr['buy'], "itunesId"=>$arr['itunesId']];
			} else {
				$timestamp = date('Y-m-d H:i:s');
				$itunes = get_itunes_link_id($arr['itunesId']);
				mysqli_query($mysqli, "UPDATE itunes SET link='" . $itunes['link'] . "',rent='" . $itunes['rent'] . "',buy='" . $itunes['buy'] . "',itunesId='" . $itunes['itunesId'] . "',timestamp='$timestamp' WHERE id=$id");
			}
		} else {
			$dirName = $director['name'] ?: "";
			$timestamp = date('Y-m-d H:i:s');
			$itunes = get_itunes_link($params['title'], $params['runtime'], $dirName, $params['year']);
			mysqli_query($mysqli, "INSERT INTO itunes VALUES ($id,'" . $itunes['link'] . "','" . $itunes['rent'] . "','" . $itunes['buy'] . "','" . $itunes['itunesId'] . "','" . date('Y-m-d H:i:s') . "')");
		}
		if (strlen($itunes['link']) > 0) {
			$text = '';
			$text .= "<span class='link-time'>" . gen_time($timestamp) . "</span>";
			$rent = explode("|", $itunes['rent']);
			$buy = explode("|", $itunes['buy']);
			if($rent[0]) $text .= "Rent: <b class='price'>$" . $rent[0] . "</b><br>";
			if($rent[1]) $text .= "Rent<sup>HD</sup>: <b class='price'>$" . $rent[1] . "</b><br>";
			if($buy[0]) $text .= "Buy: <b class='price'>$" . $buy[0] . "</b><br>";
			if($buy[1]) $text .= "Buy<sup>HD</sup>: <b class='price'>$" . $buy[1] . "</b><br>";
			if(strlen($rent[0] . $rent[1] . $buy[0] . $buy[1]) == 0) $text .= "Pre-order";
//$text .= $arr['timestamp'];
			$response = array(
				'link' => $itunes['link'],
				'text' => $text
			);
			echo json_encode($response);
		} else {
			$available = '';
			if (isset($_SESSION['user']) && ($mysqli->query("SELECT * FROM alerts WHERE id=$id AND itunes=1 AND username='".$_SESSION['user']."'")->num_rows > 0))
				$available = "We'll email you when this movie is available on iTunes.";
			else
				$available = "Want us to <a class='alert-me'><b>email you</b></a> when it becomes available?";

			echo json_encode(array('link' => '', 'text' => 'Sorry, we don\'t have an iTunes link.<br>' . $available));
		}
	}
} 