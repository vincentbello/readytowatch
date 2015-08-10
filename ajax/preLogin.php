<?php
require_once __DIR__ . '/../includes/mysqli_connect.php';
require_once __DIR__ . '/../includes/functions.php';
session_start();
$username = strip_tags($_POST['username']);
$password = hash("sha256", $_POST['password']);
$timestamp = date('Y-m-d H:i:s');
//Check for a record that matches the POSTed credentials
$query = "SELECT * FROM users WHERE username = '$username'";
$result = $mysqli->query($query);
$user = mysqli_fetch_assoc($result);
if ($result->num_rows == 0)
	echo "<span class='message-danger' style='margin-bottom: 15px'><i class='fa fa-times-circle' style='font-size:20px'></i> Sorry, this username does not exist.</span>";
else if ($user['password'] != $password)
	echo "<span class='message-danger' style='margin-bottom: 15px'><i class='fa fa-times-circle' style='font-size:20px'></i> Sorry, this password is incorrect.</span>";
else if ($user['active'] != 1)
	echo "<span class='message-danger' style='margin-bottom: 15px'><i class='fa fa-times-circle' style='font-size:20px'></i> You need to activate your account first!</span>";

if ( $result->num_rows == 1 && $user['active'] == 1 ) {
	$_SESSION['user'] = $username;
	if (strlen($user['fb_id']) > 0)
		$_SESSION['fbId'] = $user['fb_id'];
	mysqli_query($mysqli, "INSERT INTO session_history VALUES ({$user['id']}, 'login', '$timestamp')");
	if ($_POST['rememberme']) {
		onLogin($user['id']);
	}
}

?>