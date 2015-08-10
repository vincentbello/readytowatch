<?php // templates/gen_entry.php: assume we have an array $params of movie parameters ?>
<div class="mov_entry box" data-id="<?php echo $params['id'] ?>">
	
	<?php if ($loggedIn) {
		$fav = $mysqli->query("SELECT * FROM favorites WHERE user_id = {$account['id']} AND movie_id = " . $params['id'])->num_rows;
		if ($fav > 0)
			echo "<span class='fav-button favorited fav-result btn-custom' id='fav" . $params['id']. "' data-toggle='tooltip' data-placement='bottom' title='Remove from favorites'><i class='fa fa-star' style='color:white'></i></span>";
		else
			echo "<span class='fav-button fav-result btn-custom' id='fav" . $params['id']. "' data-toggle='tooltip' data-placement='bottom' title='Add to favorites'> <i class='fa fa-plus-circle'></i></span>";
	}
	?>
	
	<div class="img-box">
		<a href="movie/<?php echo $params['id'] . '/' . rewriteUrl($params['title']) ?>">
		<img class="poster" alt="<?php echo $params['title'] ?>" src="<?php echo (($params['img_link'] == '') ? 'images/no_image_found.png' : 'http://image.tmdb.org/t/p/w185' . $params['img_link']);?>"></a>
	</div>

	<div class="mov-info-short">
		<div class="left-info">
			<div class="mov-header">
				<span class="title"><a href="movie/<?php echo $params['id'] . '/' . rewriteUrl($params['title']) ?>">
				<?php echo $params['title']?></a></span>
				<?php
				if ((strlen($params['year']) > 0) && ($params['year'] != '0'))
					echo "<span class='year'>(".  $params['year'] . ")</span>";
				if (strlen($params['mpaa']) > 0)
					echo '<span class="mpaa">' . $params['mpaa'] . '</span>';
				if ($params['adult'] == 1) {
					echo '<span class="fa-stack" style="margin-left:5px" data-toggle="tooltip" data-placement="top" title="Adult movie">
						<i class="fa fa-child fa-stack-1x"></i>
						<i class="fa fa-ban fa-stack-2x" style="color:#c20427"></i>
					</span>';
				}
				?>
				<a class="more-info-button" data-toggle="tooltip" data-placement="top" title="More"><i class='fa fa-angle-double-down'></i></a>
			</div>
			<div class="imdb-rating">
				<?php
				if ($params['imdb_rating']) {
					echo '<div>'
						. $params['imdb_rating'] . '</div>
						<a href="http://www.imdb.com/title/' . $params['imdb_id'] . '" target="_blank">
							<img class="imdb-icon" alt="IMDb" src="images/imdb_icon.png">
						</a>';
				}
				?>
			</div>
			<div class="mov-info-small">
				<span>
				<?php
				if ($params['runtime'] > 0)
					echo "<i class='fa fa-clock-o'></i> " . gen_time($params['runtime']);
				if ($params['genres'] !== '') {
					echo (($params['runtime'] > 0) ? ' - ' : '') . '<i class="fa fa-film"></i> ' . gen_genres($params['genres']);
				}
				if ($params['language'] !== '')
					echo ((($params['genres'] !== '') || ($params['runtime'] > 0)) ? ' - ' : '') . '<i class="fa fa-comment-o"></i> ' . $params['language']?>
				</span>
			</div>
			<div class="ellipsis">
				<div>
					<?php echo stripslashes(fix_aps($params['synopsis'])); ?>
				</div>
			</div>

		<?php

		$numActors = sizeof($cast);
		echo "<div class='cast-short'>
				<div class='cast-actors'>";
			for($j = 0; $j < min(3, $numActors); $j++) {
				if ($cast[$j]['name'] !== "")
					echo '<div class="cast"><b><a class="actorLink" data-hovercard="'. $cast[$j]['actor_id'] .'" href="actor/' . $cast[$j]['actor_id'] . '/' . rewriteUrl($cast[$j]['name']) . '">' . stripslashes($cast[$j]['name']) . '</a></b></div>';
			}
		echo '</div>
		<div class="cast-characters">';
		for($k = 0; $k < min(3, $numActors); $k++) {
			if ($cast[$k]['character'] !== "")
				echo "<div class='char'><span class='char-indent'>" . stripslashes($cast[$k]['character']) . "</span></div>";
		}
		echo "</div>
		</div>";
		?>
		<div class="complete-cast"></div>
		<div class="complete-cast-message"><i class="fa fa-spinner fa-spin"></i></div>

	</div>
	<div class="border"></div>

	<div class="right-info">
		<div class="ajax-attempt">
			<div class="link-list result-right-info" id="id<?php echo $params['id'] ?>">
				<button class="btn-custom btn-custom-lg"><i class="fa fa-link" style="margin-right: 5px"></i> <span>Load links</span></button>
				<!-- <span><i class="fa fa-link fa-2x fa-spin"></i></span> -->
			</div>
			<div class="loading-links"><i style="margin: 30px 5px 0 30px; float:left" class="fa fa-link fa-2x fa-spin"></i></div>
			<?php
			if (($loggedIn) & ($mysqli->query("SELECT * FROM alerts WHERE id = {$params['id']} AND itunes+amazon+netflix+youtube+crackle+google_play > 0 AND user_id={$account['id']}")->num_rows > 0))
				echo "<span class='no-links'>No links available.<br>We'll email you when we have a link!<br><span class='manage-alerts'><a href='alerts'><i class='fa fa-bell'></i> Manage alerts</a></span></span>";
			else
				echo "<span class='no-links'>No links available.<br>Want us to <a class='alert-me'><b>email you</b></a> when we have a link?</span>";
			?>
		</div>
	</div>
	</div>
</div>








