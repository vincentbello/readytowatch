<?php
include 'includes/mysqli_connect.php';
include 'includes/functions.php';
include 'includes/login_check.php';
include 'includes/track_page_view.php';

date_default_timezone_set("America/New_York");
$genre = (($_GET['g']) ? urldecode($_GET['g']) : $_POST['g']);
$orderby = ($_GET['o']) ? urldecode($_GET['o']) : "popularity";
$urlFriendlyGenre = urlencode(strtolower($genre));
if ($orderby == "imdb")
	$orderQuery = "ORDER BY imdb.imdb_rating DESC";
else
	$orderQuery = "ORDER BY movies.$orderby DESC";
$timestamp = date("Y-m-d H:i:s");

$i = 0;
$countquery = "SELECT COUNT(id) FROM movies WHERE movies.genres LIKE '%$genre%'";
$movieCount = mysqli_fetch_assoc($mysqli->query($countquery))['COUNT(id)'];
$page = 1;
if (!isset($_GET['type']) || $_GET['type'] != 'actors') {
	if ($_GET['page']) {
		$page = $_GET['page'];
		$i = ($page - 1) * $numPerPage;
	}
	$searchlimit = min($movieCount, $i + $numPerPage);
}

$query = "SELECT * FROM movies INNER JOIN imdb ON movies.id=imdb.id WHERE movies.genres LIKE '%$genre%' $orderQuery LIMIT $numPerPage OFFSET $i";
$ids = array();

if($s1 = $mysqli->query($query)) {
	while($r1 = mysqli_fetch_assoc($s1)) {
		$ids[] = $r1['id'];
	}
}

$movies = getMoviesShort($ids);

gen_page_header(ucwords($genre) . " | readyto.watch");

?>

<body>

<?php

include 'includes/navbar.php';

include 'includes/left_navbar.php';

?>
<div id="container">
	
	<?php

	if ($movieCount > 0) {
		echo "<p>";
		echo "We have <b>" . (($movieCount == 1) ? "1</b> <span class=\"genre-tag\">$genre</span>movie" : number_format($movieCount) . "</b><span class=\"genre-tag\">$genre</span>movies")
		. (($movieCount > $numPerPage) ? (($i == 0) ? ": showing first $numPerPage movies." : ": showing movies $i - $searchlimit.") : ".");
		echo "</p>";
		?>

		<div class="dropdown">
		  <span class="orderby-label"><b>Order by</b></span>
		  <button id="orderby" type="button" class="btn btn-default dropdown-button" data-dropdown="orderby" aria-haspopup="true" aria-expanded="false">
		    <?php
	    	if ($orderby == 'year')
	    		echo 'Year';
	    	else if ($orderby == 'popularity')
	    		echo 'Popularity';
	    	else if ($orderby == 'imdb')
	    		echo 'IMDb Rating';
	    	else if ($orderby == 'runtime')
	    		echo 'Runtime';
		    ?>
		   <span class="caret"></span>
		  </button>
		  <ul class="dropdown-menu" id="dropdown" style="right: 0;left:auto" role="menu" aria-labelledby="orderby">
		  	<li<?php echo ($orderby == 'popularity') ? " class='active'" : "" ?>><a href='genre/<?php echo "$urlFriendlyGenre/popularity"; ?>'>Popularity</a></li>
		    <li<?php echo ($orderby == 'imdb') ? " class='active'" : "" ?>><a href='genre/<?php echo "$urlFriendlyGenre/imdb"; ?>'>IMDb Rating</a></li>
		    <li<?php echo ($orderby == 'year') ? " class='active'" : "" ?>><a href='genre/<?php echo "$urlFriendlyGenre/year"; ?>'>Year</a></li>
		    <li<?php echo ($orderby == 'runtime') ? " class='active'" : "" ?>><a href='genre/<?php echo "$urlFriendlyGenre/runtime"; ?>'>Runtime</a></li>
		  </ul>
		</div>
		
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
			$url = "genre/$urlFriendlyGenre" . (($_GET['o']) ? "/" . $_GET['o'] : "") . "/p";
			gen_pagination($current, $numPerPage, $num_pages, $url);
		}
		
		//////////////////// PAGINATION //////////////////////
	
	} else {
		echo 'We have no movies for <span class="genre-tag">'. $genre .'</span>.<br><br>';
	}

	// close the connection
	$mysqli->close();
	?>

</div> <!-- #container -->

<?php
gen_page_footer();
?>

