<?php
include '../includes/mysqli_connect.php';
session_start();
if (isset($_SESSION['user'])) {
	$user = mysqli_fetch_assoc($mysqli->query("SELECT id FROM users WHERE username='{$_SESSION['user']}'"));
} else if (isset($_SESSION['fbId'])) {
	$user = mysqli_fetch_assoc($mysqli->query("SELECT id FROM users WHERE fb_id='{$_SESSION['fbId']}'"));
}
$userId = $user['id'];

if (strlen($_POST['currentPass']) > 0) {
	$curr = hash('sha256', $_POST['currentPass']);
	$new = hash('sha256', $_POST['newPass']);
	$conf = hash('sha256', $_POST['confirmPass']);
	$pass = mysqli_fetch_assoc($mysqli->query("SELECT password FROM users WHERE id=$userId"))['password'];
	// echo "$curr and $new and $conf and $pass";
	if (($curr == $pass) && ($conf == $new)) {
		mysqli_query($mysqli, "UPDATE users SET password='$conf' WHERE id=$userId");
		echo "1";
	} else if ($pass != $curr) {
		echo "2";
	} else {
		echo "3";
	}
} else if (strlen($_POST['newEmail']) > 0) {
	$existsQuery = $mysqli->query("SELECT email FROM users WHERE email='{$_POST['newEmail']}'")->num_rows;
	if (!filter_var($_POST['newEmail'], FILTER_VALIDATE_EMAIL)) {
		echo "4";
	} else if ($existsQuery->num_rows > 0) {
		echo "5";
	} else {
		mysqli_query($mysqli, "UPDATE users SET email='{$_POST['newEmail']}' WHERE id=$userId");
		echo "6";
	}
}

// 1: success
// 2: old password doesn't match
// 3: new passwords don't match
// 4: new email address added
?>