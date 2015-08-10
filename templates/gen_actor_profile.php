<?php

	if ($backdrop)
		$src = "http://image.tmdb.org/t/p/w780" . $backdrop;
	else
		$src = "images/no_backdrop.png";
	?>
	<div class="backdrop-container">
		<div class="backdrop<?php if (!$backdrop) echo ' no-backdrop'?>" style="background-image: url('<?php echo $src ?>')"></div>
		<?php
			if ($backdropMovie)
				echo "<div class='tagline'><b><a href='movie/$backdrop_id" . "/" . rewriteUrl($backdropMovie['title']) . "'>" . $backdropMovie['title'] . "</a></b> (". $backdropMovie['year'] .")</div>";
		?>
	</div>

<div class="movie-lg actor-profile-lg box">

	<div class="img-box lg">
		<a href="actor/<?php echo $id . '/' . rewriteUrl($name)?>">
			<img class="poster-lg" alt="<?php echo $name ?>" src='<?php	echo (strlen($photo) > 0) ? "http://image.tmdb.org/t/p/w185/$photo" : "images/no_actor_found.png" ?>'>
		</a>
	</div>

	<div class="mov-info" style="margin-top:-10px">
		<div class="left-info lg">
			<div class="main-header">
				<a href="actor/<?php echo $id . '/' . rewriteUrl($name)?>"><span class="title"><?php echo $name ?></span></a>
			</div>
			<div class="mov-info-small">
				<span>
					<?php
					if ($type && $person_id !== '0')
						echo "<i class='fa fa-film'></i> " . str_replace("|", " &#183; ", $type) . " - ";
					echo birth_and_death($dob, $dod);
					?>					
				</span>
			</div>
			<div class="about original">
				<p><?php echo parse_bio($about, $name) ?></p>
				<p class="read-more"><a class="read-more-button">More...</a></p>
				<div style="clear: both"></div>
			</div>
		</div> <!-- .left-info-lg -->



		<div class="right-info lg" style="margin-top:-5px">
			<div class="starring">
				<?php
					if ($noMovies)
						echo "<span>We don't have any movies for this actor. Sorry!</span>";
					else {
						$j = 0;
						$knownForMovies = array();
						$knownForQuery = "SELECT m.id, m.title, m.year, m.img_link
					  		FROM movies m
					  		INNER JOIN roles r
					  		ON r.movie_id = m.id
					  		WHERE r.actor_id = $id
					  		ORDER BY m.popularity DESC, r.star DESC
					  		LIMIT 4";
						$q = $mysqli->query($knownForQuery);
						// $q = $mysqli->query("SELECT * FROM movies WHERE director=$id OR actor_id LIKE '%|$id' OR actor_id LIKE '$id|%' OR actor_id LIKE '%|$id|%' ORDER BY popularity DESC");
						$movieCount = $q->num_rows;

						if ($movieCount > 0) {
					 		while ($movie = mysqli_fetch_assoc($q)) {
					 			$img = (($movie['img_link'] == '') ? "images/no_image_found.png" : "http://image.tmdb.org/t/p/w185{$movie['img_link']}");
					 			$movieUrl = "movie/{$movie['id']}/" . rewriteUrl($movie['title']);
					 			echo "<div class='actor-starring'>
					 					<a href='$movieUrl'><div class='image-centered'><img alt='{$movie['title']}' src='$img'>
					 				</div></a>
					 				<div>
					 					<b><a href='$movieUrl'>{$movie['title']}</a></b> <span class='year'>{$movie['year']}</span></div>
					 				</div>";
					 				$knownForMovies[] = $movie;
					 			$j++;
					 		}
						}
					}
				?>
			</div>
			<div class="starring reveal">
			<?php if ($knownForMovies > 0) { ?>
				<b>Known for</b>: 
				<?php

				foreach($knownForMovies as $key=>$movie) {
				 	echo '<b><a href="movie/'. $movie['id'] . '/' . rewriteUrl($movie['title']) .'">' . $movie['title'] . '</a></b> (' . $movie['year'] . ')' . (($j == min(4, $movieCount) - 1) ? "." : ", ");
				}
			}
				?>

			</div>
		</div>
	</div> <!-- .mov-info -->
	<div class="mobile-mov-info reveal">
		<div class="about">
			<p><?php echo parse_bio($about, $name) ?></p>
		</div>
		<div class="starring reveal">
		<?php if ($knownForMovies > 0) { ?>
			<b>Known for</b>: 
			<?php
			foreach($knownForMovies as $key=>$movie) {
			 	echo '<b><a href="movie/'. $movie['id'] . '/' . rewriteUrl($movie['title']) .'">' . $movie['title'] . '</a></b> (' . $movie['year'] . ')' . (($key == min(4, $movieCount) - 1) ? "." : ", ");
			}
		}
		?>
		</div>
	</div>
	<div style="clear: both"></div>
</div>

<?php

function parse_bio($string, $name) {
if ($pos = strpos($string, 'Description above from')) $string = substr($string, 0, $pos);
$string = str_replace('From Wikipedia, the free encyclopedia. ', '', $string);
$string = str_replace('From Wikipedia, the free encyclopedia.', '', $string);
$string = str_replace('From Wikipedia, the free encyclopedia', '', $string);
// $i = 0;
// while (!ctype_alpha($string[0])) {
// $string = substr($string, 1);
// }
$string = str_replace('?', '-', utf8_decode($string));
if (strlen($string) < 5) {
	$string = "We currently have no biography for $name.";
}

return $string;
}

?>