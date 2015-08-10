<?php

if (isset($_GET['email']) && isset($_GET['hash'])) {
	$email = mysql_escape_string($_GET['email']);
	$hash = mysql_escape_string($_GET['hash']);
	$verify = "SELECT username, email, hash, active FROM users WHERE email='$email' AND hash='$hash' AND active=0";
	$result = $mysqli->query($verify);
	$data = mysqli_fetch_assoc($result);
	$username = $data['username'];
	$match = $result->num_rows;
	if ($match > 0) {
		mysqli_query($mysqli, "UPDATE users SET active=1 WHERE email='$email' AND hash='$hash' AND active=0");
	}
}
?>