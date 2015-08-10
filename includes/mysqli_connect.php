<?php

//$mysqli = new mysqli("localhost", "root", "30062004--Ls", "readytowatch");
$mysqli = new mysqli("localhost", "root", "root", "readytowatch");
/* check connection */
if ($mysqli->connect_errno) {
    printf("Connect failed: %s\n", $mysqli->connect_error);
    exit();
} else {
}
$mysqli->set_charset("utf8");
date_default_timezone_set("America/New_York");

?>