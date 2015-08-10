<?php

require_once '../../includes/mysqli_connect.php';
require_once '../../includes/functions.php';

$id = $_POST['id'];
$linkType = $_POST['linkType'];
$values = $_POST['values'];

$assignments = array();
foreach($values as $key => $value) {
	$assignments[] = "$key=" . ((is_numeric($value) && $key != 'itunesId') ? $value : "'$value'");
}

$query = "UPDATE $linkType SET " . implode(", ", $assignments) . " WHERE id=$id";

mysqli_query($mysqli, $query);

echo "<i class='fa fa-thumbs-o-up'></i> Changes saved.";

?>