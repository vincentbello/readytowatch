<?php
include 'includes/mysqli_connect.php';

include 'includes/functions.php';

include 'includes/login_check.php';

include 'includes/track_page_view.php';

$time_start = microtime(true);

session_start();
date_default_timezone_set("America/New_York");
$search = (($_GET['q']) ? urldecode($_GET['q']) : $_POST['q']);
$fSearch = mysql_escape_string($search);
$timestamp = date("Y-m-d H:i:s");
if ($loggedIn) {
	$q = "INSERT INTO history VALUES ({$account['id']}, '$fSearch', '$timestamp')";
	mysqli_query($mysqli,$q);

	// ADD TO user_searches
	$searchTerm = mysql_escape_string($search);
	mysqli_query($mysqli, "INSERT INTO user_searches VALUES ({$account['id']}, '$ipAddress', '$searchTerm', '$timestamp')");
}

$search_formatted = preg_replace("/[^a-z A-Z 0-9]+/", " ", $search);

$actor_ids = array();

///////// PREPARE THE SEARCH QUERY BASED ON THE USER'S PREFERENCES ////////////////////////////////

$adult = " AND adult=0";
if ($loggedIn) {
	if ($account['adult'] == 1)
		$adult = "";
}

if($s2 = $mysqli->query("SELECT id FROM actors WHERE MATCH(name) AGAINST ('$fSearch')")) {
	while($r2 = mysqli_fetch_assoc($s2)) {
		$actor_ids[] = $r2['id'];
	}
}
$i = 0;
$countquery = "SELECT COUNT(*)
				FROM movies
				WHERE MATCH(title) AGAINST ('$fSearch')
				OR title LIKE '$fSearch'
				$adult";
$movieCount = mysqli_fetch_assoc($mysqli->query($countquery))['COUNT(*)'];
$page = 1;
if (!isset($_GET['type']) || $_GET['type'] != 'actors') {
	if ($_GET['page']) {
		$page = $_GET['page'];
		$i = ($page - 1) * $numPerPage;
	}
	$searchlimit = min($movieCount, $i + $numPerPage);
}

//$searchQuery = "SELECT SQL_CALC_FOUND_ROWS m.id,m.title,m.year,m.runtime,m.genres,m.adult,m.synopsis,m.tagline,m.director,m.img_link,m.language,m.mpaa,m.imdb_id,i.imdb_rating,
$searchQuery = "SELECT id,
				CASE WHEN title = '$fSearch' THEN 1 ELSE 0 END
				AS score,
				MATCH(title) AGAINST('$fSearch%')
				AS relevance
				FROM movies
				WHERE MATCH(title) AGAINST ('$fSearch')
				OR title LIKE '$fSearch'
				$adult 
				ORDER BY score DESC, popularity DESC, relevance DESC 
				LIMIT $numPerPage 
				OFFSET $i;";

$movies = array();

$ids = array();

if ($s1 = $mysqli->query($searchQuery)) {
	while ($r1 = mysqli_fetch_assoc($s1)) {
		$ids[] = $r1['id'];
	}
}

$movies = getMoviesShort($ids);

gen_page_header("$search | readyto.watch");

?>

<body>

<?php

include 'includes/navbar.php';

$navbarPage = 2;

include 'includes/left_navbar.php';

?>
<div id="container">
	
	<ul id="results-tabs" class="nav nav-pills">
		<li<?php echo (((isset($_GET['type']) && ($_GET['type'] == 'actors')) || ((sizeof($actor_ids) > 0) && ($movieCount == 0))) ? "" : " class='active'") ?>>
			<a href="#results-movies" id="tab-movies" data-toggle="tab">Movies (<?php echo $movieCount?>)</a>
		</li>
		<li<?php echo (((isset($_GET['type']) && ($_GET['type'] == 'actors')) || ((sizeof($actor_ids) > 0) && ($movieCount == 0))) ? " class='active'" : "") ?>>
			<a href="#results-actors" id="tab-actors" data-toggle="tab">People (<?php echo sizeof($actor_ids)?>)</a>
		</li>
	</ul>
	
	<div class="tab-content">
		<div class="tab-pane fade
		<?php echo (((isset($_GET['type']) && ($_GET['type'] == 'actors')) || ((sizeof($actor_ids) > 0) && ($movieCount == 0))) ? "" : " in active") ?>
		" id="results-movies">
	
	<?php

	if ($movieCount > 0) {
		echo "Your search returned <b>" . (($movieCount == 1) ? "1</b> result" : "$movieCount</b> results")
		. (($movieCount > $numPerPage) ? (($i == 0) ? ": showing first $numPerPage movies." : ": showing movies $i - $searchlimit.") : ".");
		
		?>
		<p>Can't find the movie you were looking for? Return to <a href='http://www.readyto.watch/'>home</a>.</p>
		
		<?php
		
		foreach($movies as $movie) {
			$params = $movie['params'];
			$cast = $movie['actors'];
			include 'templates/gen_entry.php';
		}
		
		//////////////////// PAGINATION //////////////////////
	
		if ($movieCount > $numPerPage) {
			$num_pages = ceil($movieCount/$numPerPage);
			$current = $page;
			$url = "search?q=" . urlencode($search) . "&page=";
			gen_pagination($current, $numPerPage, $num_pages, $url);
		}
		
		//////////////////// PAGINATION //////////////////////
	
	} else {
		echo 'Your search returned no results. <a class="return-to-search">Try a new search</a>.<br><br>';
	}
	?>
	
	</div> <!-- #results-movies -->
	<div class="tab-pane fade
	<?php echo (((isset($_GET['type']) && ($_GET['type'] == 'actors')) || ((sizeof($actor_ids) > 0) && ($movieCount == 0))) ? " in active" : "") ?>
	" id="results-actors">
	
	<?php
	// People search
	
	////////////////////////////////////////////////////////////////////////////////////////////////////
	
	$i = 0;
	$searchlimit = $numPerPage;
	if ($_GET['from']) {
		$i = $_GET['from'];
		$searchlimit = $_GET['to'];
	}
	
	$movieCount = sizeof($actor_ids);
	if ($movieCount > 0) {
	echo "Your search returned <b>" . (($movieCount == 1) ? "1</b> actor" : "$movieCount</b> actors")
	. (($movieCount > $numPerPage) ? (($i == 0) ? ": showing first $numPerPage actors." : ": showing actors $i - $searchlimit.") : ".");
	
	?>
	<p>Can't find the actor you were looking for? <a class="return-to-search">Try a new search</a>.</p>
	
	<?php
	
	//echo "I IS $i, limit is $searchlimit<br>";
	//echo "search is $search";
	
	while (($actor_ids[$i] != "") && ($i < $searchlimit)) {
	
	if ($t = $mysqli->query("SELECT * FROM actors a INNER JOIN person_type p ON a.person_id = p.person_id WHERE id={$actor_ids[$i]}")) {
	if ($result = mysqli_fetch_assoc($t)) { // if we have this movie in the movies database
		
		include 'templates/gen_actor_result.php';
	
		}
	$i++;
	
	}
	}
	
	//////////////////// PAGINATION //////////////////////
	
	if ($movieCount > $numPerPage) {
	echo "<div class='pag-wrapper'><ul class='pagination'>";
	// PREVIOUS
	echo "<li". (($i > 5) ? "><a href='search?q=" . urlencode($search) .
		"&from=" . (ceil($i/5)-2)*5 . "&to=" . (ceil($i/5)-1)*5 . "&type=actors'>" : " class='disabled'><a>") ."&laquo;</a></li> ";
	
	$num_pages = ceil($movieCount/5);
	$current = ceil($i/5);
	
	for ($page = max($current-5,1); $page <= min($num_pages,$current+5); $page++) {
	
	if (($page == $current-5) && ($page > 1))
		echo "<li><a href='search?q=" . urlencode($search) . "&from=0&to=5&type=actors'>1</a></li><li class='disabled'><a href='#'>...</a></li> ";
	
	if (($i > (($page - 1)*5)) && ($i <= ($page*5))) {
		echo "<li class='active'><a href='#'>$page</a></li>";
	} else {
		echo "<li><a href='search?q=" . urlencode($search) . "&from=" . (($page - 1)*5) . "&to=" . min($movieCount,$page*5) . "&type=actors'>$page</a></li> ";
	}
	
	if (($page == $current+5) && ($num_pages > $page))
		echo "<li class='disabled'><a href='#'>...</a></li> <li><a href='search?q=" . urlencode($search) . "&from=" . (($num_pages - 1)*5) . "&to=$movieCount&type=actors'>$num_pages</a></li> ";
	
	}
	// NEXT
	echo "<li". (($i < $movieCount) ? "><a href='search?q=" . urlencode($search) .
		"&from=" . ceil($i/5)*5 . "&to=" . min((ceil($i/5)+1)*5,$movieCount) . "&type=actors'>" : " class='disabled'><a>") ."&raquo;</a></li> ";
	
	echo "</ul></div>";
	}
	
	//////////////////// PAGINATION //////////////////////

	
	} else {
		echo 'Your search matched no results. <a class="return-to-search">Try a new search</a>.';
	}
	////////////////////////////////////////////////////////////////////////////////////////////////////

	
	
	
	// close the connection
	$mysqli->close();
	
	?>
		</div> <!-- #results-actors -->
	</div> <!-- .tab-content -->

</div> <!-- #container -->

<?php
$addedScripts = "<script>
	$( '#nav-search' ).val('" . addslashes($search) . "');
</script>";
$time_end = microtime(true);
//echo ($time_end - $time_start);
gen_page_footer($addedScripts);
?>

