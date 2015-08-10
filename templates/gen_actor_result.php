<div class='mov_entry box' style='font-size: 90%; position: relative; width: 100%'>
<div class='img-box'>
<a href='actor/<?php echo $result['id']?>'>
<img class='poster' alt="<?php echo $result['name'] ?>" src='<?php
echo (($result['photo'] == '') ? "images/no_image_found.png" : "http://image.tmdb.org/t/p/w185/{$result['photo']}");
?>'></a>
</div>
<?php $dob = $result['dob']; $dod = $result['dod']; ?>
<div class='result-info'>
<div class='left-result'><span class='actor-name-result'><b><a href='actor/<?php echo $result['id']?>'><?php echo $result['name']?></a></b></span>
	<?php
	if ($result['label'] && $result['label'] != "N/A")
		echo "<div class=''>" . str_replace("|", " &#183; ", $result['label']) . "</div>";




	if ((strlen($dob) < 1) || ($dob == "0000-00-00")) {
	} else {
		echo "<div class='born-result'>Born ";
		echo date('F j, Y', strtotime($dob)) . 
			(((strlen($dod) < 1) || ($dod == "0000-00-00")) ? (" (age " . floor((time() - strtotime($dob)) / 31556926) . ")") : (" - Died " . date('F j, Y', strtotime($dod)) .
			" (age " . floor((strtotime($dod) - strtotime($dob)) / 31556926) . ")"));
		echo "</div>";
	}
	?>
<b class="mobile-disappear" style="float: right; margin: 20px 15px; font-size: 18px;">Known for: </b></div>
<div class='known-for' style='padding-left: 1%; border-left: 1px solid #e4e4e4'> 

<?php

$query = "SELECT m.id, m.title, m.year, m.img_link
			FROM movies m
	  		INNER JOIN roles r
	  		ON r.movie_id = m.id
	  		WHERE r.actor_id = {$actor_ids[$i]}
	  		ORDER BY m.popularity DESC, r.star DESC
	  		LIMIT 5";

if ($q = $mysqli->query($query)) {
 while ($movie = mysqli_fetch_assoc($q)) {
 	echo '<div class="movie-known-for">';
 	echo '<a href="movie/'. $movie['id'] .'"><img alt="'. $movie['title'] .'" class="poster-known-for" src="'.
 	(($movie['img_link'] == '') ? 'images/no_image_found.png' : 'http://image.tmdb.org/t/p/w185' . $movie['img_link']) . '"></a><br>';
 	echo '<div class="movie-result-title"><b><a href="movie/'. $movie['id'] .'">' . $movie['title'] .
 	'</a></b> <span class="year">' . $movie['year'] . '</span></div></div>';
 }
}
?>
</div> <!-- .known-for -->
</div> <!-- .result-info -->
<div class='overflow-alternative'></div>
</div> <!-- .mov_entry -->
<br>