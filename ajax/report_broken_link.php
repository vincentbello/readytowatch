<?php
$id = $_POST['id'];
$type = $_POST['type'];

session_start();
$user = "an anonymous user";
if ($_SESSION['user'])
	$user = $_SESSION['user'];
else if ($_SESSION['fbId'])
	$user = $_SESSION['fbId'];

$email = "vincent.bello@gmail.com";

$subject = "Broken link report";
$message = "
	A broken link was reported by <b>$user</b>.<br>
	Movie ID: <b>$id</b>
	Movie type: <b>$type</b>";
	
$headers = "From: readyto.watch\r\n";
$headers.= "MIME-Version: 1.0\r\n";
$headers.= "Content-Type: text/html; charset=ISO-8859-1\r\n";

mail($email, $subject, $message, $headers);

echo "<i class='fa fa-check-circle'></i> Thanks! We'll fix the link as soon as possible.";
?>