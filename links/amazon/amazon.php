<?php
if (isset($_POST['id'])) {
	$id = $_POST['id'];

	require_once '../../includes/mysqli_connect.php';
	require_once '../link_functions.php';

	session_start();

	$amazonDisp = true;
	if (isset($_SESSION['user'])) {
		$account = mysqli_fetch_assoc($mysqli->query("SELECT amazon_prime FROM users WHERE username='" . $_SESSION['user'] . "'"));
		if ($account['amazon_prime'] == 0)
			$amazonDisp = false;
	}
	
	// LINKS
	$params = mysqli_fetch_assoc($mysqli->query("SELECT * FROM movies WHERE id=$id"));
	$q = "SELECT * FROM amazon WHERE id=$id";
	if ($l = $mysqli->query($q)) {
		if ($arr = mysqli_fetch_assoc($l)) {
			if (((time() - strtotime($arr['timestamp']))/86400) < 14) {
				// if we have this movie in the links table and have updated it in the last seven days
				$amazon = ["link"=>$arr['link'], "rent"=>$arr['rent'], "buy"=>$arr['buy']];
			} else {
				$cast = array();
				if ($c = $mysqli->query("SELECT a.name FROM actors a INNER JOIN roles r ON a.id = r.actor_id AND r.movie_id = $id")) {
					while ($actor = mysqli_fetch_assoc($c)) {
						$cast[] = $actor['name'];
					}
				}
				$amazon = get_amazon_link($params['title'], $params['runtime'], implode('|', $cast));
				mysqli_query($mysqli, "UPDATE amazon SET link='" . $amazon['link'] . "',rent='" . $amazon['rent'] . "',buy='" . $amazon['buy'] . "',asin='".$amazon['asin']."',timestamp='" . date('Y-m-d H:i:s') . "' WHERE id=$id");
			}
		} else {
			$cast = array();
			if ($c = $mysqli->query("SELECT a.name FROM actors a INNER JOIN roles r ON a.id = r.actor_id AND r.movie_id = $id")) {
				while ($actor = mysqli_fetch_assoc($c)) {
					$cast[] = $actor['name'];
				}
			}
			$amazon = get_amazon_link($params['title'], $params['runtime'], implode('|', $cast));
			mysqli_query($mysqli, "INSERT INTO amazon VALUES ($id,'" . $amazon['link'] . "','" . $amazon['rent'] . "','" . $amazon['buy'] . "','".$amazon['asin']."','" . date('Y-m-d H:i:s') . "')");				
		}
		if ((strlen($amazon['link']) > 0) && $amazonDisp) {
			echo '<div class="link"><a target="_blank" href="' . $amazon['link']
			. '"><img data-toggle="tooltip" alt="Amazon Instant Video" title="Amazon Instant Video" data-placement="top" class="link_icon" src="images/amazon_icon.png"></a><br>';
			$rent = explode("|", $amazon['rent']);
			$buy = explode("|", $amazon['buy']);
			if($rent[0]) echo "Rent: <b class='price'>$" . $rent[0] . "</b><br>";
			if($rent[1]) echo "Rent<sup>HD</sup>: <b class='price'>$" . $rent[1] . "</b><br>";
			if($buy[0]) echo "Buy: <b class='price'>$" . $buy[0] . "</b><br>";
			if($buy[1]) echo "Buy<sup>HD</sup>: <b class='price'>$" . $buy[1] . "</b><br>";
			if(strlen($amazon['rent'].$amazon['buy']) == 0) echo "<a target='_blank' href='" . $amazon['link'] . "'>Follow link for prices</a>";
			echo "</div>";
		}
	}
}

?>