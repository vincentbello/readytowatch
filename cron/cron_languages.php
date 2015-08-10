<?php

require_once(__DIR__ . '/../includes/mysqli_connect.php');
require_once(__DIR__ . '/../includes/functions.php');

if ($l = $mysqli->query("SELECT id,language, imdb_id FROM movies WHERE language NOT LIKE 'English'")) {
	while ($movie = mysqli_fetch_assoc($l)) {
		if (strlen($movie['imdb_id']) > 0) {
			$language = get_language($movie['imdb_id']);
			if (($language != $movie['language']) && (strlen($language) > 0)) {
				mysqli_query($mysqli, "UPDATE movies SET language='$language' WHERE id={$movie['id']}");
			}
		}
	}
}

?>