<?php
include '../includes/mysqli_connect.php';
$type = $_POST['type'];
$data = $_POST['data'];

if ($type == 'user') {
	if ($mysqli->query("SELECT username FROM users WHERE username='$data'")->num_rows > 0)
		echo "<i class='fa fa-exclamation-triangle'></i> This username already exists.";
	if (!preg_match("/^[ A-Za-z0-9._-]*$/", $data))
		echo "<i class='fa fa-exclamation-triangle'></i> Please only use letters, numbers, spaces, <b>.</b>, <b>-</b>, or <b>_</b>.";
} else {
	if ($mysqli->query("SELECT email FROM users WHERE email='$data'")->num_rows > 0)
		echo "<i class='fa fa-exclamation-triangle'></i> This email address is already registered. <a href='login'>Log in</a>.";
}
?>