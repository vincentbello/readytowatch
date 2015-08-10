<?php
// {"source":"google_play","display_name":"Google Play","link":"https:\/\/play.google.com\/store\/movies\/details\/Enough_Said?id=8Xe88rDbdsM","formats":[{"price":"12.99","format":"HD","type":"purchase"},{"price":"12.99","format":"SD","type":"purchase"},{"price":"5.99","format":"HD","type":"rent"},{"price":"4.99","format":"SD","type":"rent"}]}

function getData ( $endpoint ) {
	$session = curl_init($endpoint);
	curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
	$data = curl_exec($session);
	curl_close($session);
	$m = json_decode($data);	
	return $m;
}

function idFromGPLink($link) {
	if (!strlen($link))
		return "";
	else
		return substr($link, strrpos($link, "?id=") + 4);
}

function idFromVuduLink($link) {
	if (!strlen($link))
		return "";
	else {
		$pos = strpos($link, "content%2F") + 10;
		return substr($link, $pos, strrpos($link, "%2F") - $pos);
	}
}


require_once __DIR__ . '/../includes/mysqli_connect.php';

$time_start = microtime(true); 

set_time_limit(86400);

$count = 0;
$baseurl = 'http://api-public.guidebox.com/v1.43/json/rKwMJTqLzOXE9oznPOcA2QDrXqbY6QiR';
if ($i = $mysqli->query("SELECT id FROM movies ORDER BY popularity DESC LIMIT 4500 OFFSET 4500")) {
// if ($i = $mysqli->query("SELECT id FROM movies WHERE id=858")) {
	while ($movie = mysqli_fetch_assoc($i)) {
		$count++;
		$m1 = getData("$baseurl/search/movie/id/themoviedb/" . $movie['id']);
		if ($m1 === NULL) {
			echo "Error parsing json, id " . $movie['id'];
			continue;
		}
		//$hboGo = []

		if ($m1->id) {
			$gId = $m1->id;
			// INSERT GUIDEBOX ID
			mysqli_query($mysqli, "INSERT INTO guidebox VALUES (" . $movie['id'] . ", $gId)");

			$m2 = getData("$baseurl/movie/$gId");
			if ($m2 === NULL) {
				echo "error parsing json, gId $gId";
				continue;
			}

			foreach($m2->purchase_web_sources as $movSource) {
				$source = $movSource->source;
				if ($source == 'google_play') {

					$link = $movSource->link ?: '';
					$rent = $rentHD = $buy = $buyHD = '';

					foreach($movSource->formats as $format) {
							if (($format->format == 'SD') && ($format->type == 'rent') )
								$rent = $format->price ?: "";
							else if (($format->format == 'HD') && ($format->type == 'rent'))
								$rentHD = $format->price ?: "";
							else if (($format->format == 'SD') && ($format->type == 'purchase') )
								$buy = $format->price ?: "";
							else if (($format->format == 'HD') && ($format->type == 'purchase'))
								$buyHD = $format->price ?: "";
					}
	
					$googlePlay = ["link"=>$link,"rent"=>($rent.'|'.$rentHD), "buy"=>($buy.'|'.$buyHD), "googleplay_id"=>idFromGPLink($link)];
					
					$gpQuery = "INSERT INTO google_play VALUES (". $movie['id'].",'" . $googlePlay['link'] . "','" . $googlePlay['rent'] . "','" . $googlePlay['buy'] . "','" . $googlePlay['googleplay_id'] . "','" . date('Y-m-d H:i:s') . "')";
					mysqli_query($mysqli, $gpQuery);



				} else if ($source == 'vudu') {
					$link = $movSource->link ?: '';
					$rent = $rentHD = $buy = $buyHD = '';

					foreach($movSource->formats as $format) {
							if (($format->format == 'SD') && ($format->type == 'rent') )
								$rent = $format->price ?: "";
							else if (($format->format == 'HD') && ($format->type == 'rent'))
								$rentHD = $format->price ?: "";
							else if (($format->format == 'SD') && ($format->type == 'purchase') )
								$buy = $format->price ?: "";
							else if (($format->format == 'HD') && ($format->type == 'purchase'))
								$buyHD = $format->price ?: "";
					}
	
					$vudu = ["link"=>$link,"rent"=>($rent.'|'.$rentHD), "buy"=>($buy.'|'.$buyHD), "vudu_id"=>idFromVuduLink($link)];

					$vuduQuery = "INSERT INTO vudu VALUES (". $movie['id'].",'" . $vudu['link'] . "','" . $vudu['rent'] . "','" . $vudu['buy'] . "','" . $vudu['vudu_id'] . "','" . date('Y-m-d H:i:s') . "')";
					mysqli_query($mysqli, $vuduQuery);

				}


			}

		}
	}
}

$mysqli->close();
$time_end = microtime(true);
$execution_time = ($time_end - $time_start);

// REPORTING
echo "\n---------------------------\n\n";
echo "Finished executing " . __FILE__ . " at " . date('g:i:s A') . " on " . date('F j, Y') . ".\n";
echo "Total Execution Time: " . $execution_time/60 . " minutes (" . $execution_time/3600 . " hours)";
if ($count) {
	echo "\n" . $count . " requests in " . $execution_time . " seconds means 1 request in " . ($execution_time/$count) .
	" seconds.";
}
echo "\n\n---------------------------\n";

?>