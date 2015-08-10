<?php
include 'includes/mysqli_connect.php';
include 'includes/functions.php';
include 'includes/login_check.php';
include 'includes/track_page_view.php';

gen_page_header('readyto.watch', '<style>html{height:100%}.dropdown-menu.typeahead{z-index:10000;width:530px}.footer{position:initial}.tooltip{border-style:hidden;}
	@media (max-width: 320px) { .dropdown-menu.typeahead { width: 100% } }
	</style>');
?>

<body id="home">

<?php

$navbarPage = 1;
include 'includes/left_navbar.php';

// get background image
$backgroundQuery = "SELECT p.id, p.type, m.title, m.backdrop, m.year, m.img_link FROM popular p
INNER JOIN movies m
ON m.id = p.id
WHERE p.type!='crackle'
AND LENGTH(m.backdrop) > 0
ORDER BY RAND() LIMIT 1";
//$backgroundQuery = 
if ($bgq = $mysqli->query($backgroundQuery)) {
	if ($bg = mysqli_fetch_assoc($bgq)) {
		$backgroundUrl = "http://image.tmdb.org/t/p/w780{$bg['backdrop']}";
	}
}

$availableLink = false;
if ($bg['id']) {
	$avQuery = "SELECT link FROM {$bg['type']} WHERE id={$bg['id']}";
	if ($availableQ = $mysqli->query($avQuery)) {
		if ($available = mysqli_fetch_assoc($availableQ)) {
			$availableLink = $available['link'];
			if ($bg['type'] == "itunes")
				$type = "iTunes";
			else if ($bg['type'] == "google_play")
				$type = "Google Play";
			else if ($bg['type'] == "youtube")
				$type = "YouTube";
			else
				$type = ucfirst($bg['type']);
		}
	}	
}

?>

<div style="height: 100%;min-height:100%">

	
	<?php include 'includes/home_navbar.php'; ?>
	<div id="background-wrapper">
		<div class="main">
			<div class="main-wrapper">
			  	<div class="main-fixed-height">
			  		<div class="main-fixed-centered">
					  	<form role="form" id='home-search' onSubmit='return validate_form(this);' action='search' method='get' enctype='multipart/form-data' 
					  	autocomplete="off">
						<div class='form-group'>
							<input type='text' id='search' class='form-control' name='q' maxlength='40' placeholder="SEARCH MOVIES OR PEOPLE"
							autocomplete="off">
							<button type="submit" class="btn btn-primary" id="search-button"><i class="fa fa-search fa-lg"></i></button>
							<!-- <button id='search_button' type='submit'></button> -->
						</div>
					  	</form>
				  </div>
			  	</div>

			  	<div id="background"<?php if ($backgroundUrl) echo " style='background-image: url($backgroundUrl)'"; ?>>
	   				<div class="overlay"></div>
	   			</div>
			</div>
		</div>
	
		<!-- 		<div class="table-row hugger-row">
		  	<div class="hero-hugger">
		  		<div class="read-more">
		  			<div class="contents" data-toggle="tooltip" data-placement="top" title="About Us"><a href="#description"><i class="fa fa-angle-down"></i></a></div>
		  		</div>
		  	</div>
		</div> -->
		<a href="#read-more">
  			<div id="read-more">
  				<i class="fa fa-angle-down"></i>
  			</div>
  		</a>

  		<div id="background-caption">
  			<a href='<?php echo "movie/{$bg['id']}/" . rewriteUrl($bg['title']); ?>'>
  				<img alt='<?php echo $bg['title'] ?>' src='http://image.tmdb.org/t/p/w92<?php echo $bg['img_link'] ?>'>
  			</a>
  			<a href='<?php echo "movie/{$bg['id']}/" . rewriteUrl($bg['title']); ?>'><?php echo $bg['title'] ?></a>
  			<br><?php echo $bg['year'] ?>
  			<br><?php if ($availableLink) echo "Available on <a target='_blank' href='$availableLink'>$type</a>" ?>
  		</div>
	
	</div>


	
	    <div id="pop-movies" class="home-section">
		    <!-- TABS -->
		    <div style="overflow: hidden">
			    <h1>Popular Movies</h1>
			    <!-- Nav tabs -->
				<ul class="nav nav-tabs" role="tablist" id="pop-movies-tabs">
				  <li role="presentation" class="active"><a href="#pop-itunes" role="tab" data-toggle="tab">iTunes Store</a></li>
				  <li role="presentation"><a href="#pop-amazon" role="tab" data-toggle="tab">Amazon Instant Video</a></li>
				  <li role="presentation"><a href="#pop-netflix" role="tab" data-toggle="tab">Netflix</a></li>
				  <li role="presentation"><a href="#pop-youtube" role="tab" data-toggle="tab">YouTube</a></li>
				  <li role="presentation"><a href="#pop-google-play" role="tab" data-toggle="tab">Google Play</a></li>
				  <li role="presentation"><a href="#pop-crackle" role="tab" data-toggle="tab">Crackle</a></li>
				</ul>
			</div>
			
			<!-- Tab panes -->
			<div class="tab-content">
			  	<div role="tabpanel" class="tab-pane fade in active" id="pop-itunes">
				  	<?php
				  	$itunesQuery = "SELECT movies.id, movies.title, movies.year, movies.img_link, itunes.link, itunes.rent, itunes.buy 
				  					FROM movies 
				  					JOIN popular 
				  					ON movies.id = popular.id 
				  					JOIN itunes
				  					ON popular.id = itunes.id
				  					AND popular.type = 'itunes' 
				  					LIMIT 18";
	
				  	if($itunes1 = $mysqli->query($itunesQuery)) {
						while($movThumb = mysqli_fetch_assoc($itunes1)) {
							$movUrl = $movThumb['id'] . "/" . rewriteUrl($movThumb['title']);
							$itunesRent = explode("|", $movThumb['rent']);
							$itunesBuy = explode("|", $movThumb['buy']);
					 		echo '<div class="related-mov pop-movie">
					 				<img alt="'.$movThumb['title'].'" src="' . ((strlen($movThumb['img_link'])>0) ? 'http://image.tmdb.org/t/p/w92' . $movThumb['img_link'] : 'images/no_image_found.png') . '">
					 				<div class="legend">
					 					<div class="legend-inner">
					 						<i class="fa fa-external-link"></i> <a target="_blank" href="' . $movThumb['link'] . '">iTunes</a><br>';
											if($itunesRent[0]) echo "Rent: <b class='price'>$" . $itunesRent[0] . "</b><br>";
											if($itunesRent[1]) echo "Rent<sup>HD</sup>: <b class='price'>$" . $itunesRent[1] . "</b><br>";
											if($itunesBuy[0]) echo "Buy: <b class='price'>$" . $itunesBuy[0] . "</b><br>";
											if($itunesBuy[1]) echo "Buy<sup>HD</sup>: <b class='price'>$" . $itunesBuy[1] . "</b><br>";
											if(strlen($itunesRent[0] . $itunesRent[1] . $itunesBuy[0] . $itunesBuy[1]) == 0) echo "Pre-order";
					 					echo '</div>
					 				</div>
					 				<div class="mov-caption">
					 					<a href="movie/' . $movUrl . '">' . $movThumb['title'] . '</a> <span class="year">' . $movThumb['year'] . '</span>
					 				</div>
					 			</div>';
						}
					}
				  	?>
			  	</div>
			  	<div role="tabpanel" class="tab-pane fade" id="pop-amazon">
				  	<?php
				  	$amazonQuery = "SELECT movies.id, movies.title, movies.year, movies.img_link, amazon.link, amazon.rent, amazon.buy 
				  					FROM movies 
				  					JOIN popular 
				  					ON movies.id = popular.id 
				  					JOIN amazon
				  					ON popular.id = amazon.id
				  					AND popular.type = 'amazon'
				  					LIMIT 18";
	
				  	if($amazon1 = $mysqli->query($amazonQuery)) {
						while($movThumb = mysqli_fetch_assoc($amazon1)) {
							$movUrl = $movThumb['id'] . "/" . rewriteUrl($movThumb['title']);
							$amazonRent = explode("|", $movThumb['rent']);
							$amazonBuy = explode("|", $movThumb['buy']);
					 		echo '<div class="related-mov pop-movie">
					 				<img alt="'.$movThumb['title'].'" src="' . ((strlen($movThumb['img_link'])>0) ? 'http://image.tmdb.org/t/p/w92' . $movThumb['img_link'] : 'images/no_image_found.png') . '">
					 				<div class="legend">
					 					<div class="legend-inner">
					 						<i class="fa fa-external-link"></i> <a target="_blank" href="' . $movThumb['link'] . '">Amazon</a><br>';
											if($amazonRent[0]) echo "Rent: <b class='price'>$" . $amazonRent[0] . "</b><br>";
											if($amazonRent[1]) echo "Rent<sup>HD</sup>: <b class='price'>$" . $amazonRent[1] . "</b><br>";
											if($amazonBuy[0]) echo "Buy: <b class='price'>$" . $amazonBuy[0] . "</b><br>";
											if($amazonBuy[1]) echo "Buy<sup>HD</sup>: <b class='price'>$" . $amazonBuy[1] . "</b><br>";
											if(strlen($amazonRent[0] . $amazonRent[1] . $amazonBuy[0] . $amazonBuy[1]) == 0) echo "Pre-order";
					 					echo '</div>
					 				</div>
					 				<div class="mov-caption">
					 					<a href="movie/' . $movUrl . '">' . $movThumb['title'] . '</a> <span class="year">' . $movThumb['year'] . '</span>
					 				</div>
					 			</div>';
						}
					}
				  	?>
			  	</div>
			  	<div role="tabpanel" class="tab-pane fade" id="pop-netflix">
				  	<?php
				  	$netflixQuery = "SELECT movies.id, movies.title, movies.year, movies.img_link, netflix.link 
				  					FROM movies 
				  					JOIN popular 
				  					ON movies.id = popular.id 
				  					JOIN netflix
				  					ON popular.id = netflix.id
				  					AND popular.type = 'netflix'
				  					LIMIT 18";
	
				  	if($netflix1 = $mysqli->query($netflixQuery)) {
						while($movThumb = mysqli_fetch_assoc($netflix1)) {
							$movUrl = $movThumb['id'] . "/" . rewriteUrl($movThumb['title']);
					 		echo '<div class="related-mov pop-movie">
					 				<img alt="'.$movThumb['title'].'" src="' . ((strlen($movThumb['img_link'])>0) ? 'http://image.tmdb.org/t/p/w92' . $movThumb['img_link'] : 'images/no_image_found.png') . '">
					 				<div class="legend">
					 					<div class="legend-inner">
					 						<i class="fa fa-external-link"></i> <a target="_blank" href="' . $movThumb['link'] . '">Netflix</a><br>Free (with subscription)
					 					</div>
					 				</div>
					 				<div class="mov-caption">
					 					<a href="movie/' . $movUrl . '">' . $movThumb['title'] . '</a> <span class="year">' . $movThumb['year'] . '</span>
					 				</div>
					 			</div>';
						}
					}
				  	?>
			  	</div>
			  	<div role="tabpanel" class="tab-pane fade" id="pop-youtube">
				  	<?php
				  	$youtubeQuery = "SELECT movies.id, movies.title, movies.year, movies.img_link, youtube.videoId 
				  					FROM movies 
				  					JOIN popular 
				  					ON movies.id = popular.id 
				  					JOIN youtube
				  					ON popular.id = youtube.id
				  					AND popular.type = 'youtube'
				  					LIMIT 18";
	
				  	if($youtube1 = $mysqli->query($youtubeQuery)) {
						while($movThumb = mysqli_fetch_assoc($youtube1)) {
							$movUrl = $movThumb['id'] . "/" . rewriteUrl($movThumb['title']);
					 		echo '<div class="related-mov pop-movie">
					 				<img alt="'.$movThumb['title'].'" src="' . ((strlen($movThumb['img_link'])>0) ? 'http://image.tmdb.org/t/p/w92' . $movThumb['img_link'] : 'images/no_image_found.png') . '">
					 				<div class="legend">
					 					<div class="legend-inner">
					 						<i class="fa fa-external-link"></i> <a target="_blank" href="http://www.youtube.com/watch?v=' . $movThumb['videoId'] . '">YouTube</a>
					 					</div>
					 				</div>
					 				<div class="mov-caption">
					 					<a href="movie/' . $movUrl . '">' . $movThumb['title'] . '</a> <span class="year">' . $movThumb['year'] . '</span>
					 				</div>
					 			</div>';
						}
					}
				  	?>
			  	</div>
			  	<div role="tabpanel" class="tab-pane fade" id="pop-google-play">
				  	<?php
				  	$googleQuery = "SELECT movies.id, movies.title, movies.year, movies.img_link, google_play.link, google_play.rent, google_play.buy 
				  					FROM movies 
				  					JOIN popular 
				  					ON movies.id = popular.id 
				  					JOIN google_play
				  					ON popular.id = google_play.id
				  					AND popular.type = 'google_play'
				  					LIMIT 18";
	
				  	if($google1 = $mysqli->query($googleQuery)) {
						while($movThumb = mysqli_fetch_assoc($google1)) {
							$movUrl = $movThumb['id'] . "/" . rewriteUrl($movThumb['title']);
							$googleRent = explode("|", $movThumb['rent']);
							$googleBuy = explode("|", $movThumb['buy']);
					 		echo '<div class="related-mov pop-movie">
					 				<img alt="'.$movThumb['title'].'" src="' . ((strlen($movThumb['img_link'])>0) ? 'http://image.tmdb.org/t/p/w92' . $movThumb['img_link'] : 'images/no_image_found.png') . '">
					 				<div class="legend">
					 					<div class="legend-inner">
					 						<i class="fa fa-external-link"></i> <a target="_blank" href="' . $movThumb['link'] . '">Google Play</a><br>';
											if($googleRent[0]) echo "Rent: <b class='price'>$" . $googleRent[0] . "</b><br>";
											if($googleRent[1]) echo "Rent<sup>HD</sup>: <b class='price'>$" . $googleRent[1] . "</b><br>";
											if($googleBuy[0]) echo "Buy: <b class='price'>$" . $googleBuy[0] . "</b><br>";
											if($googleBuy[1]) echo "Buy<sup>HD</sup>: <b class='price'>$" . $googleBuy[1] . "</b><br>";
											if(strlen($googleRent[0] . $googleRent[1] . $googleBuy[0] . $googleBuy[1]) == 0) echo "Pre-order";
					 					echo '</div>
					 				</div>
					 				<div class="mov-caption">
					 					<a href="movie/' . $movUrl . '">' . $movThumb['title'] . '</a> <span class="year">' . $movThumb['year'] . '</span>
					 				</div>
					 			</div>';
						}
					}
				  	?>
			  	</div>
			  	<div role="tabpanel" class="tab-pane fade" id="pop-crackle">
				  	<?php
				  	$crackleQuery = "SELECT movies.id, movies.title, movies.year, movies.img_link, crackle.link 
				  					FROM movies 
				  					JOIN popular 
				  					ON movies.id = popular.id 
				  					JOIN crackle
				  					ON popular.id = crackle.id
				  					AND popular.type = 'crackle'
				  					LIMIT 18";
	
				  	if($crackle1 = $mysqli->query($crackleQuery)) {
						while($movThumb = mysqli_fetch_assoc($crackle1)) {
							$movUrl = $movThumb['id'] . "/" . rewriteUrl($movThumb['title']);
					 		echo '<div class="related-mov pop-movie">
					 				<img alt="'.$movThumb['title'].'" src="' . ((strlen($movThumb['img_link'])>0) ? 'http://image.tmdb.org/t/p/w92' . $movThumb['img_link'] : 'images/no_image_found.png') . '">
					 				<div class="legend">
					 					<div class="legend-inner">
					 						<i class="fa fa-external-link"></i> <a target="_blank" href="' . $movThumb['link'] . '">Crackle</a><br>Free (with ads)
					 					</div>
					 				</div>
					 				<div class="mov-caption">
					 					<a href="movie/' . $movUrl . '">' . $movThumb['title'] . '</a> <span class="year">' . $movThumb['year'] . '</span>
					 				</div>
					 			</div>';
						}
					}
				  	?>
			  	</div>
			</div>

		</div> <!-- #pop-movies -->



	<div class="home-section" id="about-us">
	    <div class='cols-wrapper' style='width: 90%;margin-left: 5%'>
	      <div class='col-40 home' style='margin-top:18px'>
        		<h2><i class="fa fa-play" style="margin-right: 5px"></i> Watch your favorite movies</h2>
        		<p>
        			<img alt="readyto.watch" src="images/logo_text_w150.png" style="margin-top:-3px"> allows you to search for movies 	across a huge library, and view their availability on various 
	        		<b>purchase</b>, <b>rental</b>, and <b>streaming</b> services.
	        	</p>
	      </div>
	      <div class='col-35'>
	      	<h2><i class="fa fa-envelope" style="margin-right: 5px"></i> Sign up for email alerts</h2>
	        <p>
	          We're always up to date on current and upcoming movies. We can <b>notify you</b> when the movie you want is available on your favorite websites.
	        </p>
	      </div>
	    </div>
      	<div id="currentservices">
        	<h2><i class="fa fa-star" style="margin-right: 5px"></i> We proudly support <i class="fa fa-star"></i></h2>
	      	<!-- <div class='col-40 home home-logos'> -->
	     	<div class='home-logos'>
		     	<div class='logo'>
		      		<img alt="iTunes Store" data-toggle="tooltip" data-placement="top" title="iTunes Store" src="images/itunes_icon.png">
		      		<div class='logo-caption'>iTunes</div>
		      	</div>
	      		<div class='logo'>
	      			<img alt="Amazon Instant Video" data-toggle="tooltip" data-placement="top" title="Amazon Instant Video" src="images/amazon_icon.png">
	      			<div class='logo-caption'>Amazon</div>
	      		</div>
	      		<div class='logo'>
	      			<img alt="Netflix" data-toggle="tooltip" data-placement="top" title="Netflix" src="images/netflix_icon.png">
	      			<div class='logo-caption'>Netflix</div>
	      		</div>	      		
	      		<div class='logo'>
	      			<img alt="YouTube Movies" data-toggle="tooltip" data-placement="top" title="YouTube Movies" src="images/youtube_icon.png">
	      			<div class='logo-caption'>YouTube</div>
	      		</div>	      
	      		<div class='logo'>
	      			<img alt="Crackle" data-toggle="tooltip" data-placement="top" title="Crackle" src="images/crackle_icon.png">
	      			<div class='logo-caption'>Crackle</div>
	      		</div>
	      		<div class='logo'>
	      			<img alt="Google Play Store" data-toggle="tooltip" data-placement="top" title="Google Play Store" src="images/googleplay_icon.png">
	      			<div class='logo-caption'>Google Play</div>
	      		</div>
	      		<div class='logo disabled'>
	      			<img alt="Hulu Plus" data-toggle="tooltip" data-placement="top" title="Hulu Plus - Coming Soon!" src="images/huluplus_icon.png">
	      			<div class='logo-caption'>Hulu Plus</div>
	      		</div>
	      		<div class='logo disabled'>
	      			<img alt="HBO Go" data-toggle="tooltip" data-placement="top" title="HBO Go - Coming Soon!" src="images/hbogo_icon.png">
	      			<div class='logo-caption'>HBO Go</div>
	      		</div>
      		</div>	
	   	</div>
	</div>

		<?php include 'includes/footer.php' ?>
		<div id="return-to-top" data-toggle="tooltip" data-placement="top" title="Top"><a href="#home"><i class="fa fa-angle-up"></i></a></div>
	  </div>
	<?php
	// close the connection
	$mysqli->close();
	?>
</div>  <!-- #container -->

<?php

$addedScripts = "<script>

function triggerSearch () {
    $('#search').focus();
}

$(document).ready( function () {

	$('#read-more').addClass('focused');
	
	$( window ).scroll( function () {
		if ($(this).scrollTop() >= ($('#background').outerHeight() - 60)) {
			if ($('#home-navbar').attr('id') != 'navbar') {
				$('#home-navbar').attr('id', 'navbar');
				//$('body').removeAttr('id');
			}
		} else {
			if ($('#navbar').attr('id') != 'home-navbar') {
				$('#navbar').attr('id', 'home-navbar');
				//$('body').attr('id', 'home');
			}
		}
	})

	$('#background .overlay').addClass('lighter');

	triggerSearch();

	$('.return-to-search').click( function () {
		if ($('.navigator').hasClass('nav-selected'))
			$('.navigator').click();
		triggerSearch();
	})

	$('#pop-movies-tabs a').click(function (e) {
  		e.preventDefault()
  		$(this).tab('show')
	});

});
</script>";

gen_page_footer($addedScripts, true);

?>