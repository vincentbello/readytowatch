<?php
require_once('includes/mysqli_connect.php');
require_once('links/link_functions.php');
$title = "Titanic";
$runtime = 194;
$cast = "Kate Winslet|Leonardo DiCaprio|Frances Fisher|Bernard Hill";

$link = get_amazon_link($title, $runtime, $cast);


?>