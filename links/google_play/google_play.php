<?php
if (isset($_POST['id'])) {
	$id = $_POST['id'];

	require_once '../../includes/mysqli_connect.php';
	require_once '../link_functions.php';
	
	// LINKS
	$params = mysqli_fetch_assoc($mysqli->query("SELECT * FROM movies WHERE id=$id"));
	$q = "SELECT * FROM google_play WHERE id=$id";
	if ($l = $mysqli->query($q)) {
		if ($arr = mysqli_fetch_assoc($l)) {
			if (((time() - strtotime($arr['timestamp']))/86400) < 14) {
				// if we have this movie in the links table and have updated it in the last seven days
				$google_play = ["link"=>$arr['link'], "rent"=>$arr['rent'], "buy"=>$arr['buy']];
			} else {
				if (verify_google_play($arr['googleplay_id'])) {
					$google_play = ["link"=>$arr['link'], "rent"=>$arr['rent'], "buy"=>$arr['buy']];
					mysqli_query($mysqli, "UPDATE google_play SET timestamp='" . date('Y-m-d H:i:s') . "' WHERE id=$id");
				} else {
					$google_play = ["link"=>"", "rent"=>"", "buy"=>""];
					mysqli_query($mysqli, "UPDATE google_play SET link='',rent='',buy='',timestamp='" . date('Y-m-d H:i:s') . "' WHERE id=$id");
				}
			}
		} else {
			$google_play = ["link"=>"", "rent"=>"", "buy"=>""];
			mysqli_query($mysqli, "INSERT INTO google_play VALUES ($id, '', '', '', '','" . date('Y-m-d H:i:s') . "')");	
		}
		if (strlen($google_play['link']) > 0) {
			echo '<div class="link"><a target="_blank" href="' . $google_play['link']
			. '"><img data-toggle="tooltip" alt="Google Play Store" title="Google Play Store" data-placement="top" class="link_icon" src="images/googleplay_icon.png"></a><br>';
			$rent = explode("|", $google_play['rent']);
			$buy = explode("|", $google_play['buy']);
			if($rent[0]) echo "Rent: <b class='price'>$" . $rent[0] . "</b><br>";
			if($rent[1]) echo "Rent<sup>HD</sup>: <b class='price'>$" . $rent[1] . "</b><br>";
			if($buy[0]) echo "Buy: <b class='price'>$" . $buy[0] . "</b><br>";
			if($buy[1]) echo "Buy<sup>HD</sup>: <b class='price'>$" . $buy[1] . "</b><br>";
			if(strlen($google_play['rent'].$google_play['buy']) == 0) echo "<a target='_blank' href='" . $google_play['link'] . "'>Follow link for prices</a>";
			echo "</div>";
		}
	}
}

?>