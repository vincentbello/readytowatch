<?php

header('Content-type: application/json');

require_once '../../includes/mysqli_connect.php';
require_once '../../includes/functions.php';

$id = $_POST['id'];

$platforms = array('itunes', 'amazon', 'netflix', 'youtube', 'crackle', 'google_play');
$links = array();

foreach ($platforms as $p) {

	$query = "SELECT * FROM $p WHERE id=$id";

	if ($l = $mysqli->query($query)) {
		if ($link = mysqli_fetch_assoc($l)) {
			$links[$p] = $link;
		}
	}
}

echo json_encode($links);

?>