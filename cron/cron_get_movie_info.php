<?php // assume you have a movie id $id. Also assume you have included includes/functions.php (so you have getData)
// you should have an array of queries at the end: one for movies, one for keywords, one for movie_keywords, one for imdb,  $moviesQuery at the end, to insert the movie.

// check if we already have the movie. Assume not
$movCount = $mysqli->query("SELECT id FROM movies WHERE id=$id")->num_rows;
$alreadyHaveMovie = false;
// if ($movCount != 0) {
// 	$alreadyHaveMovie = true;
// }

$m = getData("https://api.themoviedb.org/3/movie/$id?api_key=$apikey");
if ($m === NULL) {
	echo "Error parsing json, id $id";
	continue;
}

if (count($m) > 0) { // if the movie id is valid

	$director = 0;
	$mpaa = $backdrop = $imdb_rating = '';
	$queryArr = array(); // ARRAY OF QUERIES

	$c = getData("https://api.themoviedb.org/3/movie/$id/credits?api_key=$apikey");
	if ($c === NULL) {
		echo "NULL CAST";
	} else {
		foreach($c->crew as $k) {
			if ($k->job == "Director") {
				$director = $k->id;
				break;
			}
		}
	}
	
	$r = getData("https://api.themoviedb.org/3/movie/$id/releases?api_key=$apikey");
	if ($r === NULL) {
		echo "Error parsing ratings, id $id";
	} else {
		foreach($r->countries as $country) {
			if ($country->iso_3166_1 == "US") {
				$mpaa = $country->certification;
				break;
			}
		}
	}
			
	$videos = getData("https://api.themoviedb.org/3/movie/$id/releases?api_key=$apikey");
	if ($videos === NULL) {
		echo "Error parsing ratings, id $id";
	} else {
		foreach($videos->results as $res) {
			if (($res->site == 'YouTube') && ($res->type == 'Trailer')) {
				$trailer = $res->key;
				break;
			}
		}
	}

	$title = mysql_escape_string($m->title);
	$year = substr($m->release_date,0,4);
	$release_date = $m->release_date;
	$runtime = $m->runtime;
	$gs = array();
	foreach($m->genres as $g) {
		$gs[] = $g->name;
	}
	$genres = mysql_escape_string(implode(" | ",$gs));
	$synopsis = mysql_escape_string($m->overview);
	
	$images = getData("https://api.themoviedb.org/3/movie/$id/images?api_key=$apikey");
	if ($images === NULL) {
		echo "Error parsing images, id $id";
	} else {
		foreach($images->backdrops as $bd) {
			if ($bd->aspect_ratio > 1.7) {
				$backdrop = $bd->file_path;
				break;
			}
		}
	}
			
	$img_link = $m->poster_path;

	$imdb_id = $m->imdb_id;
	if (strlen($imdb_id) > 0) {
		$imdb_rating = get_imdb_rating($imdb_id);
		$language = get_language($imdb_id);
	} else {
		$language = ucfirst(mysql_escape_string($m->spoken_languages[0]->name));
	}
	$popularity = $m->popularity;
	$revenue = $m->revenue;
	$tagline = mysql_escape_string($m->tagline);
	if ($m->adult == "true")
		$adult = 1;
	else
		$adult = 0;
	$timestamp = date('Y-m-d H:i:s');

	// keywords
	$keywordData = getData("https://api.themoviedb.org/3/movie/$id/keywords?api_key=$apikey");
	if ($keywordData === NULL) {
		echo "Error parsing keywords, id $id";
		continue;
	}

	if ($keywordData->keywords) {
		foreach ($keywordData->keywords as $keyword) {
			$queryArr[] = "INSERT INTO keywords VALUES (" . $keyword->id . ", '" . $keyword->name . "')";
			$queryArr[] = "INSERT INTO movie_keywords VALUES ($id, " . $keyword->id . ")";
		}
	}

	foreach($gs as $g) {
		if ($genre = $mysqli->query("SELECT keyword_id FROM keywords WHERE keyword LIKE '$g'")) {
  			if ($genreEntry = mysqli_fetch_assoc($genre)) {
  				$queryArr[] = "INSERT INTO movie_keywords VALUES ($id, " . $genreEntry['keyword_id'] . ")";
  			} else {
  				$queryArr[] = "INSERT INTO keywords VALUES (" . substr(number_format(time() * mt_rand(),0,'',''),0,8) . ", '$g')";
  			}
		}
	}

	//$queryArr[] = "DELETE FROM movies WHERE id=$id";
	$queryArr[] = $alreadyHaveMovie ? "UPDATE movies SET title='$title',year=$year,release_date='$release_date',runtime=$runtime,genres='$genres',
										adult=$adult,synopsis='$synopsis',tagline='$tagline',director='$director',img_link='$img_link',language='$language',
										mpaa='$mpaa',revenue=$revenue,imdb_id='$imdb_id', trailer='$trailer',popularity=$popularity,backdrop='$backdrop'
										WHERE id=$id"
									: "INSERT INTO movies VALUES ($id,'$title',$year,'$release_date',$runtime,'$genres',$adult,'$synopsis',
										'$tagline','$director','$img_link','$language','$mpaa', $revenue,
										'$imdb_id','$trailer',$popularity,'$backdrop')";

	$queryArr[] = $alreadyHaveMovie ? "UPDATE imdb SET imdb_rating='$imdb_rating', timestamp='$timestamp' WHERE id=$id"
									: "INSERT INTO imdb VALUES ($id, '$imdb_rating', '$timestamp')";

// related movies

$relatedQuery = "SELECT mvs.id
          FROM movie_keywords mvs
          JOIN movie_keywords mvs2
          ON mvs2.id = $id 
          AND mvs.keyword_id = mvs2.keyword_id
          JOIN movies m
          ON m.id = mvs.id
          GROUP BY mvs.id
          HAVING COUNT(*) >= 2
          ORDER BY COUNT(*) DESC
          LIMIT 18";

if($relatedMovs = $mysqli->query($relatedQuery)) {
  	while($relatedMov = mysqli_fetch_assoc($relatedMovs)) {
   		$queryArr[] = "INSERT INTO related VALUES ($id, {$relatedMov['id']})";
   	}
}

$queryArr[] = "DELETE FROM related WHERE id = related_id AND id=$id";

if ($i = $mysqli->query("SELECT * FROM related WHERE id=$id")) {
  while ($mov = mysqli_fetch_assoc($i)) {
    $queryArr[] = "INSERT INTO related VALUES({$mov['related_id']}, $id)";
  }
}

// roles
$roleQueries = array();
foreach ($c->cast as $key => $actor) {
    $char = $actor->character;
    $star = ($key <= 2) ? (3 - $key) : 0;
    $roleQueries[] = "(". $actor->id .", $id, '$char', $star)";
}
if (sizeof($roleQueries) > 0) {
	$queryArr[] = "INSERT INTO ROLES VALUES " . implode(', ', $roleQueries);
}

}

?>