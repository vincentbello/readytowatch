<?php //search
include 'includes/mysqli_connect.php';

include 'includes/functions.php';

include 'includes/login_check.php';

if (!$loggedIn)
	header('Location: login');

include 'includes/track_page_view.php';

gen_page_header('Favorites | readyto.watch');

?>

<body>

<?php
include 'includes/navbar.php';

$navbarPage = 6;
include 'includes/left_navbar.php';

?>
<div id="container">
	<h1><?php echo ($fb) ? $account['fb_fname'] : $account['username']; ?>'s Favorites</h1>

<?php

$countquery = "SELECT COUNT(movie_id) FROM favorites WHERE user_id={$account['id']}";
$total = mysqli_fetch_assoc($mysqli->query($countquery))['COUNT(movie_id)'];
$i = 0;
$page = 1;
if ($_GET['page']) {
	$page = $_GET['page'];
	$i = ($page - 1) * $numPerPage;
}
$searchlimit = min($total, $i + $numPerPage);

if ($total > 0) {

	echo "You have <span id='fav-count'><b>" . (($total == 1) ? "1</b> favorite movie</span>" : "$total</b> favorite movies</span>")
	. (($total > $numPerPage) ? (($i == 0) ? ": showing first <span id='shown'>$numPerPage</span> movies." : ": showing movies $i - <span id='shown'>$searchlimit</span>.") : ".");
	
	?>
	
	<button type="button" class="btn btn-default help-popover-btn" data-container="body" data-html="true" data-toggle="popover" data-placement="bottom" 
	title="<b>Adding a movie to your favorites</b>" 
	data-content="<p>Click the little plus symbol <i class='fa fa-plus-circle' style='color:#c20427;cursor:pointer'></i> at the bottom of a movie's poster and a star <i class='fa fa-star' style='color:#c20427;cursor:pointer'></i> will appear.</p>
	<p>To remove it, just click the star again.</p>">
	  <i class="fa fa-lightbulb-o" style="color: #c20427; margin-right: 5px; font-size: 18px"></i> How do I favorite movies?
	</button>
	
	<?php
		$query = "SELECT * FROM movies INNER JOIN favorites ON movies.id=favorites.movie_id WHERE favorites.user_id={$account['id']} LIMIT $numPerPage OFFSET $i";

		if ($movie = $mysqli->query($query)) {
			while ($result = mysqli_fetch_assoc($movie)) {
				$params = $result;
				include 'templates/gen_entry.php';
			}
		}
	
	//////////////////// PAGINATION //////////////////////
	
	if ($total > $numPerPage) {
		$num_pages = ceil($total/$numPerPage);
		$current = $page;
		$url = "favorites/p";
		gen_pagination($current, $numPerPage, $num_pages, $url);
	}
	
	//////////////////// PAGINATION //////////////////////

} else {
echo 'You currently have no favorites!<br><br>';
}

// close the connection
$mysqli->close();
?>

</div> <!-- #container -->

<?php
gen_page_footer();
?>