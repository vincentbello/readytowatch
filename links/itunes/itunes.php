<?php

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
			if (((time() - strtotime($arr['timestamp']))/86400) < 14) {
				// if we have this movie in the links table and have updated it in the last seven days
				$itunes = ["link"=>$arr['link'], "rent"=>$arr['rent'], "buy"=>$arr['buy'], "itunesId"=>$arr['itunesId']];
			} else {
				$itunes = get_itunes_link_id($arr['itunesId']);
				mysqli_query($mysqli, "UPDATE itunes SET link='" . $itunes['link'] . "',rent='" . $itunes['rent'] . "',buy='" . $itunes['buy'] . "',itunesId='" . $itunes['itunesId'] . "',timestamp='" . date('Y-m-d H:i:s') . "' WHERE id=$id");
			}
		} else {
			$itunes = get_itunes_link($params['title'], $params['runtime'], $director['name'], $params['year']);
			mysqli_query($mysqli, "INSERT INTO itunes VALUES ($id,'" . $itunes['link'] . "','" . $itunes['rent'] . "','" . $itunes['buy'] . "','" . $itunes['itunesId'] . "','" . date('Y-m-d H:i:s') . "')");
		}
		if (strlen($itunes['link']) > 0) {
			echo '<div class="link"><a target="_blank" href="' . $itunes["link"]
			. '">';
			echo '<img alt="iTunes Store" data-toggle="tooltip" title="iTunes Store" class="link_icon" src="images/itunes_icon.png"></a><br>';
			$rent = explode("|", $itunes['rent']);
			$buy = explode("|", $itunes['buy']);
			if($rent[0]) echo "Rent: <b class='price'>$" . $rent[0] . "</b><br>";
			if($rent[1]) echo "Rent<sup>HD</sup>: <b class='price'>$" . $rent[1] . "</b><br>";
			if($buy[0]) echo "Buy: <b class='price'>$" . $buy[0] . "</b><br>";
			if($buy[1]) echo "Buy<sup>HD</sup>: <b class='price'>$" . $buy[1] . "</b><br>";
			if(strlen($rent[0] . $rent[1] . $buy[0] . $buy[1]) == 0) echo "Pre-order";
			echo "</div>";
		}
	}
} 