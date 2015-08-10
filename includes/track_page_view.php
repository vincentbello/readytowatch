<?php // MUST BE AFTER 'includes/functions.php', 'includes/mysqli_connect.php'

$_SESSION['url'] = $_SERVER['REQUEST_URI'];
$ipAddress = get_ip();
$userId = ($loggedIn) ? $account['id'] : "'UNREGISTERED'";
$timestamp = date("Y-m-d H:i:s");
$pageViewed = $_SERVER['PHP_SELF'];
mysqli_query($mysqli, "INSERT INTO page_views VALUES ($userId, '$ipAddress', '$pageViewed', '$timestamp')");

?>