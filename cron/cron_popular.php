<?php

require_once(__DIR__ . '/../includes/mysqli_connect.php');

mysqli_query($mysqli, "TRUNCATE TABLE popular");

$timestamp = date('Y-m-d H:i:s');

$itunesQuery = "SELECT movies.id 
				FROM movies 
				JOIN itunes 
				ON movies.id = itunes.id 
				WHERE LENGTH(itunes.link) > 0 
				AND movies.popularity > 4
				ORDER BY movies.popularity DESC
				LIMIT 18";

if($itunes1 = $mysqli->query($itunesQuery)) {
	while($movThumb = mysqli_fetch_assoc($itunes1)) {
		mysqli_query($mysqli, "INSERT INTO popular VALUES (" . $movThumb['id'] . ", 'itunes', '$timestamp')");
	}
}

$amazonQuery = "SELECT movies.id
				FROM movies 
				JOIN amazon 
				ON movies.id = amazon.id 
				WHERE LENGTH(amazon.link) > 0 
				AND movies.popularity > 4 
				ORDER BY movies.popularity DESC
				LIMIT 18";
if($amazon1 = $mysqli->query($amazonQuery)) {
	while($movThumb = mysqli_fetch_assoc($amazon1)) {
		mysqli_query($mysqli, "INSERT INTO popular VALUES (" . $movThumb['id'] . ", 'amazon', '$timestamp')");
	}
}

$netflixQuery = "SELECT movies.id
				FROM movies 
				JOIN netflix 
				ON movies.id = netflix.id 
				WHERE LENGTH(netflix.link) > 0 
				AND movies.popularity > 1
				ORDER BY movies.popularity DESC
				LIMIT 18";
if($netflix1 = $mysqli->query($netflixQuery)) {
	while($movThumb = mysqli_fetch_assoc($netflix1)) {
		mysqli_query($mysqli, "INSERT INTO popular VALUES (" . $movThumb['id'] . ", 'netflix', '$timestamp')");
	}
}

$youtubeQuery = "SELECT movies.id
				FROM movies 
				JOIN youtube 
				ON movies.id = youtube.id 
				WHERE LENGTH(youtube.videoId) > 0 
				AND movies.popularity > 4
				ORDER BY movies.popularity DESC
				LIMIT 18";
if($youtube1 = $mysqli->query($youtubeQuery)) {
	while($movThumb = mysqli_fetch_assoc($youtube1)) {
		mysqli_query($mysqli, "INSERT INTO popular VALUES (" . $movThumb['id'] . ", 'youtube', '$timestamp')");
	}
}

$googleQuery = "SELECT movies.id
				FROM movies 
				JOIN google_play 
				ON movies.id = google_play.id 
				WHERE LENGTH(google_play.link) > 0 
				ORDER BY movies.popularity DESC 
				LIMIT 18";
if($google1 = $mysqli->query($googleQuery)) {
	while($movThumb = mysqli_fetch_assoc($google1)) {
		mysqli_query($mysqli, "INSERT INTO popular VALUES (" . $movThumb['id'] . ", 'google_play', '$timestamp')");
	}
}

$crackleQuery = "SELECT movies.id
				FROM movies 
				JOIN crackle 
				ON movies.id = crackle.id 
				WHERE LENGTH(crackle.link) > 1
				ORDER BY movies.popularity DESC 
				LIMIT 18";
if($crackle1 = $mysqli->query($crackleQuery)) {
	while($movThumb = mysqli_fetch_assoc($crackle1)) {
		mysqli_query($mysqli, "INSERT INTO popular VALUES (" . $movThumb['id'] . ", 'crackle', '$timestamp')");
	}
}

?>