<?php

if (isset($_GET['r']) && isset($_POST['resetPass'])) {
	$resetPass = true;
	$email = $_POST['changedEmail'];
	$newPassword = hash('sha256', $_POST['resetPass']);
	mysqli_query($mysqli, "UPDATE users SET password='$newPassword',resetHash='' WHERE email='$email'");
}



?>