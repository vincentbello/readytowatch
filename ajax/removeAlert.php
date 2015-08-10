<?php

require_once '../includes/mysqli_connect.php';
session_start();
$id = $_POST['id'];
if (isset($_SESSION['user'])) {
	$user = mysqli_fetch_assoc($mysqli->query("SELECT id FROM users WHERE username='{$_SESSION['user']}'"));
} else if (isset($_SESSION['fbId'])) {
	$user = mysqli_fetch_assoc($mysqli->query("SELECT id FROM users WHERE fb_id='{$_SESSION['fbId']}'"));
}
$userId = $user['id'];

mysqli_query($mysqli, "DELETE FROM alerts WHERE id=$id AND user_id=$userId");

?>