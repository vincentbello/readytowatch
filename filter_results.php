<?php

include 'includes/functions.php';
include 'includes/mysqli_connect.php';
include 'includes/navbar.php';
include 'includes/login_check.php';
include 'includes/track_page_view.php';

$arr = array('mintime' => $_GET["runtime-min"],
				'maxtime' => $_GET["runtime-max"],
				'minyear' => str_replace('< ', '', $_GET["year-min"]),
				'maxyear' => $_GET["year-max"],
				'language' => $_GET["language"],
				'genre' => $_GET["genre"],
				'mpaa' => $_GET["mpaa"],
				'orderby' => $_GET["orderby"]);

$i = 0;
$query = filter_query($arr, 'movies.id');
$total = $mysqli->query($query)->num_rows;
$page = 1;
if ($_GET['page']) {
	$page = $_GET['page'];
	$i = ($page - 1) * $numPerPage;
}
$searchlimit = min($total, $i + $numPerPage);

$ids = array();

if($s1 = $mysqli->query($query . " LIMIT $numPerPage OFFSET $i")) {
	while($r1 = mysqli_fetch_assoc($s1)) {
		$ids[] = $r1['id'];
	}
}

$movies = getMoviesShort($ids);


gen_page_header('Filter Search Results | readyto.watch');

$navbarPage = 5;
include 'includes/left_navbar.php';

?>

<body>
<div id="container">
	<?php
	
	if ($total > 0) {
		echo "Your search returned <b>" . (($total == 1) ? "1</b> result:" : "$total</b> results:")
		. " showing " . (($total > $numPerPage) ? (($i == 0) ? "first $numPerPage movies." : "movies " . ($i + 1) . " - $searchlimit.") : "all $total movie".(($total == 1) ? ".":"s."));
		?>
	
		<p>Can't find the movie you were looking for? Return to <a href='search/filter'>the Filter Search</a>.</p>
		
		<?php
		
		foreach($movies as $movie) {
			$params = $movie['params'];
			$cast = $movie['actors'];
			include 'templates/gen_entry.php';
		}
		
		//////////////////// PAGINATION //////////////////////
		
		if ($total > $numPerPage) {
			$url = gen_url($arr);
			$num_pages = ceil($total/$numPerPage);
			$current = $page;
			$paginationUrl = "filter?$url&page=";
			gen_pagination($current, $numPerPage, $num_pages, $paginationUrl);
		}
	} else {
		echo '<br>Your search returned no results. <a href="search/filter">Try another search!</a><br><br>';
	}
	
	// close the connection
	$mysqli->close();
	
	?>
</div> <!-- #container -->

<?php 
	gen_page_footer();
?>