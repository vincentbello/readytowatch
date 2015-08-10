<?php
include '../includes/mysqli_connect.php';
session_start();

$fb = isset($_SESSION['fbId']);
$dbAccountFinder = $fb ? "fb_id='{$_SESSION['fbId']}'" : "username='{$_SESSION['user']}'";

$adult = ($_GET['adult'] == "true") ? "1" : "0";
$amazon = ($_GET['amazon'] == "true") ? "1" : "0";
$netflix = ($_GET['netflix'] == "true") ? "1" : "0";
mysqli_query($mysqli, "UPDATE users SET adult=$adult,amazon_prime=$amazon,netflix=$netflix WHERE $dbAccountFinder");
?>