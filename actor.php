<?php

include 'includes/functions.php';

$id = $_GET['actorid'];

$orderby = ($_GET['o']) ? urldecode($_GET['o']) : "year";
if ($orderby == "imdb")
	$orderQuery = "ORDER BY i.imdb_rating DESC";
else
	$orderQuery = "ORDER BY m.$orderby DESC";

include 'includes/mysqli_connect.php';

include 'includes/login_check.php';

include 'includes/track_page_view.php';

$query1 = "SELECT * FROM actors a INNER JOIN person_type p ON a.person_id = p.person_id WHERE id=$id";
if($t = $mysqli->query($query1)) {
	if ($actor = mysqli_fetch_assoc($t)) {
		$name = $actor['name'];
		$photo = $actor['photo'];
		$about = $actor['about'];
		$dob = $actor['dob'];
		$dod = $actor['dod'];
		$imdb_id = $actor['imdb_id'];
		$backdrop = $actor['backdrop'];
		$backdrop_id = $actor['backdrop_id'];
		$person_id = $actor['person_id'];
		$type = $actor['label'];
	}
}

$noResult = false;
if ($t->num_rows == 0)
	$noResult = true;

gen_page_header((($noResult) ? "Not valid" : $name) . " | readyto.watch");

$backdropQuery = $mysqli->query("SELECT title, year FROM movies WHERE id=$backdrop_id");
if ($backdropQuery->num_rows > 0)
	$backdropMovie = mysqli_fetch_assoc($backdropQuery);

$acted = false;
$directed = false;
if ($person_id == 3) {
	$acted = true;
	$directed = true;
} else if ($person_id == 1) {
	$acted = true;
} else if ($person_id == 2) {
	$directed = true;
}

// $countActorQuery = "SELECT COUNT(movies.id)
// 				FROM movies 
// 				INNER JOIN roles
// 				ON movies.id=roles.movie_id
// 				WHERE roles.actor_id=$id";
// $totalQuery = mysqli_fetch_assoc($mysqli->query($countActorQuery));
// $totalActor = $totalQuery['COUNT(movies.id)'];
// $i = 0;
// $page = 1;
// if ($_GET['page']) {
// 	$page = $_GET['page'];
// 	$i = ($page - 1) * $numPerPage;
// }
// $searchlimit = min($totalActor, $i + $numPerPage);

?>

<body>

<?php
include 'includes/navbar.php';

include 'includes/left_navbar.php';
?>

<div id="container">

	<?php
	
	if (!$noResult) {
	
		include 'templates/gen_actor_profile.php';
	
		?>
		
		<h2 style='border-bottom:1px solid #e3e3e3'>Filmography</h2>
		

		<div role="tabpanel" style="margin-bottom: 10px">

			<!-- Nav tabs -->
			<ul class="nav nav-tabs" role="tablist">
				<?php if($acted) { ?>
				<li role="presentation" class="active"><a href="#acted" aria-controls="acted" role="tab" data-toggle="tab">Actor</a></li>
				<?php }
				if ($directed) { ?>
				<li role="presentation"<?php if (!$acted) echo " class='active'" ?>><a href="#directed" aria-controls="directed" role="tab" data-toggle="tab">Director</a></li>
				<?php } ?>
			</ul>
	
			<!-- Tab panes -->
			<div class="tab-content box">
				<?php if($acted) { ?>
				<div role="tabpanel" class="tab-pane fade in active" id="acted">
		
					<div class="dropdown orderby" style="margin-top:-42px">
					<span class='orderby-label'><b>Order by</b></span>
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
					  	<li<?php echo ($orderby == 'year') ? " class='active'" : "" ?>><a href='<?php echo "actor/$id&o=year"; ?>'>Year</a></li>
					    <li<?php echo ($orderby == 'popularity') ? " class='active'" : "" ?>><a href='<?php echo "actor/$id&o=popularity"; ?>'>Popularity</a></li>
					    <li<?php echo ($orderby == 'imdb') ? " class='active'" : "" ?>><a href='<?php echo "actor/$id&o=imdb"; ?>'>IMDb Rating</a></li>
					    <li<?php echo ($orderby == 'runtime') ? " class='active'" : "" ?>><a href='<?php echo "actor/$id&o=runtime"; ?>'>Runtime</a></li>
					  </ul>
					</div>
		
					<?php
					$adult = " AND m.adult=0 AND m.genres NOT LIKE '%Erotic%'";
					if (isset ($_SESSION['user'])) {
						$account = mysqli_fetch_assoc($mysqli->query("SELECT adult FROM users WHERE username='" . $_SESSION['user'] . "'"));
						if ($account['adult'] == 1)
							$adult = "";
					}

					$moviesActedQuery = "SELECT m.id, m.title, m.year, m.img_link, r.character" . (($orderby == "runtime") ? ", m.runtime" : "") . (($orderby == "imdb") ? ", i.imdb_rating" : "") . "
						FROM movies m
						INNER JOIN roles r
						ON m.id = r.movie_id
						" . (($orderby == "imdb") ? "INNER JOIN imdb i ON i.id=m.id" : "") . "
						WHERE r.actor_id = $id
						$orderQuery";
		
					if ($movie = $mysqli->query($moviesActedQuery)) {
						while ($mov = mysqli_fetch_assoc($movie)) {
							$movUrl = "{$mov['id']}/" . rewriteUrl($mov['title']);
							$movImg = (strlen($mov['img_link'])) ? "http://image.tmdb.org/t/p/w185{$mov['img_link']}" : "images/no_image_found.png";
					 		echo "<div class='related-mov pop-movie actor-film'><a href='movie/$movUrl'>
					 				<div>
					 					<img alt='{$mov['title']}' src='$movImg'>
					 					<div class='legend'>
					 						<div class='legend-inner'>"
					 							. (($mov['character']) ? "<span>As</span> <b>{$mov['character']}</b><br>" : "")
					 							. (($orderby == "imdb") ? "<img alt='IMDb' class='imdb' src='images/imdb_white.png'> <strong>{$mov['imdb_rating']}</strong>" : (($orderby == "runtime") ? "Runtime: <b>" . gen_time($mov['runtime']) . "</b>" : "")) .
					 						"</div>
					 					</div>
					 					<div>
					 						<strong>{$mov['title']}</strong> <span class='year'>{$mov['year']}</span>
					 					</div>
					 				</div>
					 			</a></div>";
						}
					}	
					?>
				</div>
				<?php }
				if ($directed) { ?>
				<div role="tabpanel" class="tab-pane fade in<?php if (!$acted) echo " active" ?>" id="directed">
					<div class="dropdown orderby" style="margin-top:-38px">
					  <span class='orderby-label'><b>Order by</b></span>
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
					  	<li<?php echo ($orderby == 'year') ? " class='active'" : "" ?>><a href='<?php echo "actor/$id&o=year"; ?>'>Year</a></li>
					    <li<?php echo ($orderby == 'popularity') ? " class='active'" : "" ?>><a href='<?php echo "actor/$id&o=popularity"; ?>'>Popularity</a></li>
					    <li<?php echo ($orderby == 'imdb') ? " class='active'" : "" ?>><a href='<?php echo "actor/$id&o=imdb"; ?>'>IMDb Rating</a></li>
					    <li<?php echo ($orderby == 'runtime') ? " class='active'" : "" ?>><a href='<?php echo "actor/$id&o=runtime"; ?>'>Runtime</a></li>
					  </ul>
					</div>
		
					<?php
					$adult = " AND adult=0 AND genres NOT LIKE '%Erotic%'";
					if (isset ($_SESSION['user'])) {
						$account = mysqli_fetch_assoc($mysqli->query("SELECT adult FROM users WHERE username='" . $_SESSION['user'] . "'"));
						if ($account['adult'] == 1)
							$adult = "";
					}

					$moviesDirectedQuery = "SELECT m.id, m.title, m.year, m.img_link" . (($orderby == "runtime") ? ", m.runtime" : "") . (($orderby == "imdb") ? ", i.imdb_rating" : "") . "
						FROM movies m
						" . (($orderby == "imdb") ? "INNER JOIN imdb i ON i.id=m.id" : "") . " WHERE director = $id $orderQuery";
		
					if ($movie = $mysqli->query($moviesDirectedQuery)) {
						while ($mov = mysqli_fetch_assoc($movie)) {
							$movUrl = "{$mov['id']}/" . rewriteUrl($mov['title']);
							$movImg = (strlen($mov['img_link'])) ? "http://image.tmdb.org/t/p/w185{$mov['img_link']}" : "images/no_image_found.png";
					 		echo "<div class='related-mov pop-movie actor-film'><a href='movie/$movUrl'>
					 				<div>
					 					<img alt='{$mov['title']}' src='$movImg'>
					 					<div class='legend'>
					 						<div class='legend-inner'>"
					 							. (($orderby == "imdb") ? "<img alt='IMDb' class='imdb' src='images/imdb_white.png'> <strong>{$mov['imdb_rating']}</strong>" : (($orderby == "runtime") ? "Runtime: <b>" . gen_time($mov['runtime']) . "</b>" : "")) .
					 						"</div>
					 					</div>
					 					<div>
					 						<strong>{$mov['title']}</strong> <span class='year'>{$mov['year']}</span>
					 					</div>
					 				</div>
					 			</a></div>";
						}
					}
					?>
				</div>
				<?php } ?>
			</div>
		
		</div>

		<?php
		if (!$acted && !$directed) {
		echo "<br><br> We have no movies with <b>$name</b>. Sorry!<br><a class='return-to-search'>Return to search</a>.<br><br>";
		}
	
	} else {
	  echo "<div class='message-danger'><i class='fa fa-exclamation-triangle'></i> This is not a valid person! <a class='return-to-search'>Try another search</a>.</div>";
	}	
	
	// close the connection
	$mysqli->close();
		
	?>

</div> <!-- #container -->

<?php

$addedScript = "<script>
$( document ).ready( function () {
	$('.tagline').hide();
  	$( window ).scrollTop($('.img-box.lg').offset().top - 80);
});

// read-more animation
var button = $('.about:visible .read-more-button'),
    p = button.parent(),
    lg = $('.movie-lg:visible'),
    offset = lg.offset().top,
    abt = $('.about:visible'),
    total = abt.height(),
    trailerHeight = 0,
    star = $('.starring:visible'),
    mobile = $('.mobile-mov-info.reveal');
var starHeight = (star.length) ? (star.offset().top + star.height() - offset - 5) : 0;
var maximum = Math.max(200, starHeight, mobile.offset().top + mobile.height() - offset);
var abtmax = maximum - abt.position().top;
abt.css('max-height', abtmax);

if (total <= abtmax) {
	p.hide();
  	abt.css('max-height', 9999);
}

button.click(function() {
	// fade out read-more
  	p.hide();  
  	abt.css({'max-height' : 9999}); 
	// prevent jump-down
	return false;
			
});

$( window ).scroll( function () {
	if ($(this).scrollTop() < 150)
		$('.tagline').fadeIn(700);
	else
		$('.tagline').fadeOut(700);
});

$('#nav-search').val('" . addslashes($name) . "');
$('#nav-search').css('font-weight', 'bold');

</script>";

gen_page_footer($addedScript);

?>