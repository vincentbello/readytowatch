<?php // gen_movie_page.php: assume we have an array $params of movie parameters, and $names, $photos, $chars, $cast_ids
	if ($params['backdrop'])
		$src = "http://image.tmdb.org/t/p/w780" . $params['backdrop'];
	else
		$src = "images/no_backdrop.png";
	?>
	<div class="backdrop-container">
		<div class="backdrop<?php if (!$params['backdrop']) echo ' no-backdrop'?>" style="background-image: url('<?php echo $src ?>')"></div>
		<?php
			if ($params['tagline'])
				echo "<div class='tagline'><i class='fa fa-quote-left'></i>" . $params['tagline'] . "<i class='fa fa-quote-right'></i></div>";
		?>
	</div>
<div class="movie-lg box" <?php if ($params['trailer']) echo 'style="min-height:247px"'; ?>>
	<?php
		if ($loggedIn) {
			$fav = $mysqli->query("SELECT * FROM favorites WHERE user_id={$account['id']} AND movie_id = $id")->num_rows;
			if ($fav > 0)
				echo "<span class='fav-button btn-custom favorited' id='fav$id' data-toggle='tooltip' data-placement='bottom' title='Remove from favorites'><i class='fa fa-star' style='color:white'></i> <span>Favorite</span></span>";
			else
				echo "<span class='fav-button btn-custom' id='fav$id' data-toggle='tooltip' data-placement='bottom' title='Add to favorites'> <i class='fa fa-plus-circle'></i> <span>Favorites</span></span>";
		}
		if ($params['trailer']) {
	?>
	
	<div class='trailer btn-custom' data-toggle="modal" data-target="#trailerModal">
		<i class='fa fa-play-circle'></i> <span>Trailer</span>
	</div>

	<div class="modal fade" id="trailerModal">
	  <div class="modal-dialog">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
	        <h4 class="modal-title">Trailer - <b><?php echo $params['title'] . "</b> (" . $params['year'] . ")"; ?></h4>
	      </div>
	      <div class="modal-body">
	      	<iframe id="trailer" class="ytplayer" height="315" width="560" src="//www.youtube.com/embed/<?php echo $params['trailer']?>?enablejsapi=1" frameborder="0" allowfullscreen></iframe>
		  </div>
	    </div><!-- /.modal-content -->
	  </div><!-- /.modal-dialog -->
	</div><!-- /.modal -->

	<?php
		} else {
			echo "<div class='trailer no-trailer'></div>";
		} // if $params['trailer']
	?>

	<div class="img-box lg">
		<a href="movie/<?php echo $id . '/' . rewriteUrl($params['title']) ?>">
			<img class="poster-lg" alt="<?php echo $params['title'] ?>" src="<?php echo (($params['img_link'] == '') ? 'images/no_image_found.png' : 'http://image.tmdb.org/t/p/w185' . $params['img_link']);?>">
		</a>
	</div>

	<div class="mov-info">
		<div class="left-info lg">
			<div class="main-header">
				<a href="movie/<?php echo $id . '/' . rewriteUrl($params['title'])?>"><span class="title"><?php echo $params['title']; ?></span></a>
				<?php
				if ((strlen($params['year']) > 0) && ($params['year'] != '0'))
					echo "<span class='year lg'>(".  $params['year'] . ")</span>";
				if (strlen($params['mpaa']) > 0)
					echo '<span class="mpaa" style="font-size:15px; margin-left:8px">' . $params['mpaa'] . '</span>';
				if ($params['adult'] == 1) {
					echo '<span class="fa-stack" style="margin-left:5px;top:-4px" data-toggle="tooltip" data-placement="top" title="Adult movie">
						<i class="fa fa-child fa-stack-1x"></i>
						<i class="fa fa-ban fa-stack-2x" style="color:#c20427"></i>
						</span>';
				}
				?>
			</div>
			<div class="mov-info-small">
				<span>
					<?php
					if ($params['runtime'] > 0)
						echo "<i class='fa fa-clock-o'></i> " . gen_time($params['runtime']);
					if ($params['genres'] !== '')
						echo (($params['runtime'] > 0) ? ' - ' : '') . '<i class="fa fa-film"></i> ' . gen_genres($params['genres']);
					if ($params['language'] !== '')
						echo ((($params['genres'] !== '') || ($params['runtime'] > 0)) ? ' - ' : '') . '<i class="fa fa-comment-o"></i> ' . $params['language'];
					?>
				</span>
			</div>
			<div class="about original">
				<p><?php
				echo stripslashes(fix_aps($params['synopsis']));
				?></p>
				<p class="read-more"><a class="read-more-button">More...</a></p>
				<div style="clear: both"></div>
			</div>
		</div> <!-- .left-info-lg -->
		<div class="right-info lg" style="margin-top:-5px">
			<div class="starring">
				<?php
					if ($noActors)
						echo "<div class='none'><i class='fa fa-exclamation-triangle'></i> We don't have a list of actors for this movie. Sorry!</div>";
					else {
						for ($i = 0; $i < min(4, sizeof($actors)); $i++) {
							echo "<div class='actor-starring'>
									<a href='actor/" . $actors[$i]['actor_id'] . "/" . rewriteUrl($actors[$i]['name']) . "'>
									<div class='image-centered'>
									<img alt='" . $actors[$i]['name'] . "' src='" . ((strlen($actors[$i]['photo']) > 0) ? 'http://image.tmdb.org/t/p/w185' . $actors[$i]['photo'] : "images/no_actor_found.png")
									. "'>
									</div>
									</a>
									<div class='actor-starring-caption'><a href='actor/" . $actors[$i]['actor_id'] . "/" . rewriteUrl($actors[$i]['name']) . "'>" . $actors[$i]['name'] . "</a></div>". stripslashes($actors[$i]['character']) . 
									"</div>";
						}
					}
				?>
			</div>
			<?php //print_r($actors); ?>
			<div class="starring reveal">
				<b>Starring</b>: 
				<?php
					if (!$noActors) {
						for ($i = 0; $i < min(4, sizeof($actors)); $i++) {
							echo "<b><a href='actor/" . $actors[$i]['actor_id'] . "/" . rewriteUrl($actors[$i]['name']) . "'>" . $actors[$i]['name'] . "</a></b> (". stripslashes($actors[$i]['character']) . ")" . (($i == min(4, sizeof($actors)) - 1) ? "." : ", ");
						}
					}
				?>

			</div>
		</div>
	</div> <!-- .mov-info -->
	<div class="mobile-mov-info reveal">
		<div class="about">
			<p><?php echo stripslashes(fix_aps($params['synopsis'])) ?></p>
		</div>
		<div class="starring reveal">
			<b>Starring</b>: 
			<?php
				if (!$noActors) {
					for ($i = 0; $i < min(4, sizeof($actors)); $i++) {
						echo "<b><a href='actor/" . $actors[$i]['actor_id'] . "/" . rewriteUrl($actors[$i]['name']) . "'>" . $actors[$i]['name'] . "</a></b> (". stripslashes($actors[$i]['character']) . ")" . (($i == min(4, sizeof($actors)) - 1) ? "." : ", ");
					}
				}
			?>
		</div>
	</div>
	<div style="clear: both"></div>
</div>

<div class="movie-section">
	<h2>Links</h2>
	<?php
	if ($admin) {
		echo "<span class='admin-edit' data-edit-type='links' data-toggle='modal' data-target='#admin-links'><i class='fa fa-user-md'></i> Edit Links</span>";
	}
	?>
	<div class="links-container" id="id<?php echo $id ?>">
		<div class="link-section box" id="itunes">
			<a target="_blank">
				<h3>iTunes Store</h3>
				<img alt="iTunes Store" src="images/itunes_icon.png">
			</a>
			<div class="link-section-info">
				<i class="fa fa-cog fa-2x fa-spin link-page-spinner"></i> Doing stuff...
			</div>
			<div class="link-feedback">Is the link broken? Let us know.</div>
		</div>
		<div class="link-section box" id="amazon">
			<a target="_blank">
				<h3>Amazon Instant Video</h3>
				<img alt="Amazon Instant Video" src="images/amazon_icon.png">
			</a>
			<div class="link-section-info">
				<i class="fa fa-cog fa-2x fa-spin link-page-spinner"></i> Doing stuff...
			</div>
			<div class="link-feedback">Is the link broken? Let us know.</div>
		</div>
		<div class="link-section box" id="netflix">
			<a target="_blank">
				<h3>Netflix</h3>
				<img alt="Netflix" src="images/netflix_icon.png">
			</a>
			<div class="link-section-info">
				<i class="fa fa-cog fa-2x fa-spin link-page-spinner"></i> Doing stuff...
			</div>
			<div class="link-feedback">Is the link broken? Let us know.</div>
		</div>
		<div class="link-section box" id="youtube">
			<a target="_blank">
				<h3>YouTube</h3>
				<img alt="YouTube" src="images/youtube_icon.png">
			</a>
			<div class="link-section-info">
				<i class="fa fa-cog fa-2x fa-spin link-page-spinner"></i> Doing stuff...
			</div>
			<div class="link-feedback">Is the link broken? Let us know.</div>
		</div>
		<div class="link-section box" id="crackle">
			<a target="_blank">
				<h3>Crackle</h3>
				<img alt="Crackle" src="images/crackle_icon.png">
			</a>
			<div class="link-section-info">
				<i class="fa fa-cog fa-2x fa-spin link-page-spinner"></i> Doing stuff...
			</div>
			<div class="link-feedback">Is the link broken? Let us know.</div>
		</div>
		<div class="link-section box" id="google_play">
			<a target="_blank">
				<h3>Google Play Store</h3>
				<img alt="Google Play Store" src="images/googleplay_icon.png">
			</a>
			<div class="link-section-info">
				<i class="fa fa-cog fa-2x fa-spin link-page-spinner"></i> Doing stuff...
			</div>
			<div class="link-feedback">Is the link broken? Let us know.</div>
		</div>
	</div>
</div>

<?php
if ($admin) {

	$platforms = array('iTunes', 'Amazon', 'Netflix', 'YouTube', 'Crackle', 'Google Play');

	?>

	<div class="admin-modal modal fade" id="admin-links">
	  <div class="modal-dialog">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        <h4 class="modal-title">Edit Links</h4>
	      </div>
	      <div class="modal-body">
			<ul class="nav nav-tabs">

			<?php
			for ($i = 0; $i < sizeof($platforms); $i++) {
				$pLong = $platforms[$i];
				$p = str_replace(" ", "_", strtolower($pLong));
				echo "<li role='presentation' id='$p'" . (($i == 0) ? " class='active'" : "") . "><a href='#admin-$p' data-toggle='tab'>$pLong</a></li>";
			}
			?>

			</ul>
			<div class="tab-content box">
				<?php
				for ($i = 0; $i < sizeof($platforms); $i++) {
					$pLong = $platforms[$i];
					$p = str_replace(" ", "_", strtolower($pLong));
					echo "<div role='tabpanel' class='tab-pane fade" . (($i == 0) ? " in active" : "") . "' id='admin-$p'>";
					echo "<button class='btn btn-primary btn-lg' data-link-type='$p'>Update link</button><span class='message-success'></span></div>";
				}
				?>
			</div>
	        
	      </div>
	    </div><!-- /.modal-content -->
	  </div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	


	<?php
	
	}
?>

<div class="movie-section">
	<h2>Information</h2>
	<div class="info-container">
		<div class="info-section box">
			<div class="info-label">Title</div>
			<div class="info-line">
				<?php echo $params['title'] ?>
			</div>
			<div class="info-label">Release</div>
			<div class="info-line">
				<?php
				echo (($params['release_date']) && ($params['release_date'] != "0000-00-00")) ? date('F j, Y', strtotime($params['release_date'])) : 'N/A';
				?>
			</div>
			<div class="info-label">Runtime</div>
			<div class="info-line">
				<?php echo ($params['runtime']) ? $params['runtime'] . " minutes" : 'N/A' ?>
			</div>
			<?php
			if ($movie['director']['name']) {
				?>
				<div class="info-label">Director</div>
				<div class="info-line">
					<?php echo "<a href='actor/" . $params['director'] . "/" . rewriteUrl($movie['director']['name']) . "'>" . $movie['director']['name'] . "</a>"; ?>
				</div>
				<?php
			}
			if ($params['genres'] != "") {
			?>
			<div class="info-label">Genres</div>
				<div class="info-line" style="line-height: 1.8">
				<?php
				foreach (explode(' | ', $params['genres']) as $genre)
					echo "<a href='genre/". urlencode(strtolower($genre)) ."'><span class='genre-tag'>$genre</span></a> ";
				?>
			</div>
			<?php } ?>
			<div class="info-label">Language</div>
			<div class="info-line">
				<?php echo $params['language'] ?: "N/A" ?>
			</div>
			<div class="info-label">Adult</div>
			<div class="info-line">
				<?php echo ($params['adult']) ? "Yes" : "No" ?>
			</div>
			<div class="info-label">Revenue</div>
			<div class="info-line">
				<?php echo format_revenue($params['revenue']) ?>
			</div>
			<?php
			if ($params['imdb_id'] && $params['imdb_rating'] != "0.0") {
				$rating = ($params['imdb_rating']) ? $params['imdb_rating'] : "N/A";
				?>
				<div class="info-label">
					<a href="http://www.imdb.com/title/<?php echo $params['imdb_id'] ?>" target="_blank">
						<img width="60" alt="IMDb" src="images/imdb_icon.png">
					</a>
				</div>
				<div class="info-line">
					<b><?php echo $params['imdb_rating'] ?></b> <span style='font-size:14px'>/10</span>
				</div>

			<?php
			}
			$keywords = array();

			if ($movie['keywords']) {
				foreach($movie['keywords'] as $k) {
					if (strlen($k['keyword']) > 0)
						$keywords[] = "<a href='keyword/{$k['keyword_id']}/". urlencode($k['keyword']) ."'>{$k['keyword']}</a>";
				}
			}
			if (count($keywords) > 0) {
				echo "<div class='info-label'>Keywords</div>";
				echo "<div class='info-line'>". implode(" &#183; ", $keywords) ."</div>";
			}
			?>

		</div>
		<div class="info-section box">
			<div class="info-label">Synopsis</div>
			<div class="info-line">
				<?php echo stripslashes(fix_aps($params['synopsis'])) ?>
			</div>
		</div>
	</div>
</div>

<div class="movie-section">
	<h2>Cast</h2>
	<div class="cast-container box"<?php if ($noActors) echo 'style="height:auto"'; ?>>
	<?php
	if (!$noActors) {
		echo "<div class='cast-scroll-left'><i class='fa fa-angle-left'></i></div>";
		for($key = 0; $key < sizeof($actors); $key++) {
			?>
			<div class="actor-starring">
				<a href="actor/<?php echo $actors[$key]['actor_id'] . '/' . rewriteUrl($actors[$key]['name']) ?>">
					<div class="image-centered">
						<img alt="<?php echo $actors[$key]['name'] ?>" src="<?php echo (strlen($actors[$key]['photo'])>0) ? 'http://image.tmdb.org/t/p/w185' . $actors[$key]['photo'] : 'images/no_actor_found.png' ?>">
					</div>
				</a>
				<div class="actor-starring-caption">
					<a href="actor/<?php echo $actors[$key]['actor_id'] . '/' . rewriteUrl($actors[$key]['name']) ?>">
						<?php echo $actors[$key]['name'] ?>
					</a>
				</div><?php echo stripslashes($actors[$key]['character']) ?>
			</div>
		
			<?php
		}
		if (sizeof($actors) > 6) echo "<div class='cast-scroll-right'><i class='fa fa-angle-right'></i></div>";
	} else {
		echo "<div class='none'><i class='fa fa-exclamation-triangle'></i> We don't have a list of actors for this movie. Sorry!</div>";
	}
	?>

	</div>
</div>

<div class="movie-section">
	<h2>Related Movies</h2>
	<div class="related-container box" id="id<?php echo $id ?>">


		<div class="more-related" style="margin-top:0;width:100%">
			<div class="related"><i class="fa fa-spinner fa-spin"></i> Loading related movies...</div>
		</div>


	</div>
</div>