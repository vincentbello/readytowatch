<?php

include '../includes/mysqli_connect.php';
session_start();
if (isset($_SESSION['user'])) {
	$user = mysqli_fetch_assoc($mysqli->query("SELECT id FROM users WHERE username='{$_SESSION['user']}'"));
} else if (isset($_SESSION['fbId'])) {
	$user = mysqli_fetch_assoc($mysqli->query("SELECT id FROM users WHERE fb_id='{$_SESSION['fbId']}'"));
}
$userId = $user['id'];

foreach($_POST as $key=>$value) {
	$query = "UPDATE alerts SET ";
	foreach($value as $type=>$alert) {
		$query .= "$type=$alert,";
	}
	$query = rtrim($query, ",") . " WHERE user_id=$userId AND id=$key";
	mysqli_query($mysqli, $query);
}
?>