<?php // assume you have an actor id $id. Also assume you have included includes/functions.php (so you have getData)
// you should have an array of queries at the end: one for movies, one for keywords, one for movie_keywords, one for imdb,  $moviesQuery at the end, to insert the movie.

	// check if we already have this person in DB
	$count = mysqli_fetch_assoc($mysqli->query("SELECT COUNT(id) FROM actors WHERE id=$id"));
	$doNotHave = ($count['COUNT(id)'] == 0) ? true : false;

	$queryArr = array();

	if ($doNotHave) {

		$a = getData("https://api.themoviedb.org/3/person/$id?api_key=$apikey");
		if ($a === NULL) {
			echo 'Error parsing json';
			continue;
		}
		if (strlen($a->name) > 0) {
			$name = $a->name;
			$person_id = getPersonType($id);
			$photo = $a->profile_path;
			$about = $a->biography;
			$dob = $a->birthday;
			$dod = $a->deathday;
			$imdb_id = $a->imdb_id;
			//mysqli_query($mysqli, "DELETE FROM actors WHERE id=$id");
			
			$b = getData("https://api.themoviedb.org/3/person/$id/tagged_images?api_key=$apikey");
			$backdrop = "";
			$backdrop_id = 0;
			if ($b->results[0]) {
				foreach ($b->results as $bd) {
					if ($bd->aspect_ratio > 1.7) {
						$backdrop = $bd->file_path;
						$backdrop_id = $bd->media->id;
						break;
					}
				}
			}

			$queryArr[] = "INSERT INTO actors VALUES ($id, $person_id, '$name', '$photo', '" . addslashes($about) . "', '$dob', '$dod', '$imdb_id', '$backdrop', '$backdrop_id')";
		
		}

	}

?>