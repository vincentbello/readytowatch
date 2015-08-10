<?php
require_once __DIR__ . '/includes/mysqli_connect.php';
session_start();
$timestamp = date('Y-m-d H:i:s');

if (isset($_SESSION['user'])) {
	$user = mysqli_fetch_assoc($mysqli->query("SELECT id FROM users WHERE username='{$_SESSION['user']}'"));
	$prevUser = $_SESSION['user'];
}

if (isset($_COOKIE['rememberme'])) {
	unset($_COOKIE['rememberme']);
	setcookie('rememberme', '', time() - 3600, '/');
}

if (isset($_SESSION['fbId'])) {
	$user = mysqli_fetch_assoc($mysqli->query("SELECT id FROM users WHERE fb_id='{$_SESSION['fbId']}'"));
}

mysqli_query($mysqli, "INSERT INTO session_history VALUES ({$user['id']}, 'logout', '$timestamp')");

session_unset();
session_destroy();

session_start();
$_SESSION['user_prev'] = $prevUser;

header("Location: ./");

?>