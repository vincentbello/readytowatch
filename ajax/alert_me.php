<?php

include_once '../includes/mysqli_connect.php';
include_once '../includes/functions.php';
session_start();

$id = $_POST['id'];
$type = $_POST['type'];
if (isLoggedIn($_SESSION)) {

	if (isset($_SESSION['user'])) {
		$user = mysqli_fetch_assoc($mysqli->query("SELECT id FROM users WHERE username='{$_SESSION['user']}'"));
	} else if (isset($_SESSION['fbId'])) {
		$user = mysqli_fetch_assoc($mysqli->query("SELECT id FROM users WHERE fb_id='{$_SESSION['fbId']}'"));
	}
	$userId = $user['id'];

	if ($type != 'any') {
		$query = "INSERT INTO alerts VALUES ($id, $userId, 0, 0, 0, 0, 0, 0, 0)";
		mysqli_query($mysqli, $query);
		mysqli_query($mysqli, "UPDATE alerts SET $type=1 WHERE id=$id AND user_id=$userId");
		echo "<span class='message-success'><i class='fa fa-check-circle'></i> OK!</span> We'll email you when this movie is available on ". ucfirst($type) .".
		<br><span class='manage-alerts'><a href='alerts'><i class='fa fa-bell'></i> Manage alerts</a></span>";
	} else {
		mysqli_query($mysqli, "INSERT INTO alerts VALUES ($id, $userId, 1, 1, 1, 1, 1, 1, 1)");
		echo "<span class='message-success'><i class='fa fa-check-circle'></i> OK!</span> We'll email you when this movie is available.<br><span class='manage-alerts'><a href='alerts'><i class='fa fa-bell'></i> Manage alerts</a></span>";
	}
} else {
	echo "Sorry, you must <a href='login'><b>log in</b></a> or <a href='signup'><b>sign up</b></a> to do that.";
}
?>