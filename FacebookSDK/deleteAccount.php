<?php
require_once __DIR__ . "/includes/mysqli_connect.php";
$timestamp = date('Y-m-d H:i:s');

session_start();

// get user ID
if (isset($_SESSION['fbId'])) {
	$user = mysqli_fetch_assoc($mysqli->query("SELECT id FROM users WHERE fb_id='{$_SESSION['fbId']}'"));
} else if (isset($_SESSION['user'])) {
	$user = mysqli_fetch_assoc($mysqli->query("SELECT id FROM users WHERE username='{$_SESSION['user']}'"));
} else {
	header("Location: ./");
}
$userId = $user['id'];

// log out
session_unset();

$_SESSION['fbId'] = null;
$_SESSION['user'] = null;

$logOutQueries = array();

$logOutQueries[] = "DELETE FROM users WHERE id=$userId";
$logOutQueries[] = "INSERT INTO session_history VALUES ($userId, 'delete', '$timestamp')";
$logOutQueries[] = "DELETE FROM favorites WHERE user_id=$userId";
$logOutQueries[] = "DELETE FROM alerts WHERE user_id=$userId";

foreach($logOutQueries as $q) {
	mysqli_query($mysqli, $q);
}

header("Location: ./");

?>