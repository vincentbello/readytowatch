<?php

session_unset();

$_SESSION['fbId'] = null;
$_SESSION['fbFirstName'] = null;
$_SESSION['fbLastName'] = null;
$_SESSION['fbEmail'] = null;

mysqli_query($mysqli, "INSERT INTO session_history VALUES ({$account['id']}, 'logout', '$timestamp')");

?>