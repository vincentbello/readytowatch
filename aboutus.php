<?php
include 'includes/functions.php';
include 'includes/mysqli_connect.php';
include 'includes/login_check.php';
include 'includes/track_page_view.php';
gen_page_header('About Us | readyto.watch');
?>

<body>
<?php
include 'includes/navbar.php';

$navbarPage = 7;
include 'includes/left_navbar.php';
?>
<div id="container">

	<div class="logo-header">
		<img src="images/readytowatch.png">
	</div>
	
	<div class="box about-content">	
		<h2>Our Mission</h2>
		<p>
			Watching a movie in the age of the Internet shouldn't be hard. You can find virtually any movie ever made, and stream it online instantly; but that process isn't always easy.
		</p>
		<p>
			You dig through websites like IMDb or Rotten Tomatoes for hours, only to find that the rare gem you haven't seen yet isn't available on Netflix. So you venture over to the iTunes Store, where it's only available for purchase at a scandalous $19.99. You go over to Amazon, and huzzah! It's free... for Amazon Prime subscribers. You finally give up, furious, teary-eyed.
			But look &#8212; <i>it's not your fault</i>.
		</p>
		<p>
			At <img src="images/logo_text.png" alt="readyto.watch" width="130">, we are building a platform that unites all of the resources you need for a painless movie-hunting experience. Search for a movie in our huge library, and we'll give you all the information you need, from a plot summary to its availability and prices on the most popular online rental/purchase services.
		</p>
		<p>
			We currently support the iTunes Store, Amazon Instant Video, Netflix, YouTube Movies and Crackle. Soon, we'll be able to show results from the Google Play Store, and Hulu Plus.
			We are still working actively on adding new features, so keep checking in.
		</p>
		<p>
			If you have any questions, suggestions, or want life advice, feel free to email us at <a href="mailto:support@readyto.watch" target="_top"><b>support@readyto.watch</b></a>.
		</p>
		<p>
			Happy watching!
		</p>
		
		<a href="mailto:vince@readyto.watch"><img src="images/vince.png" width="120" style="border-radius:60px; border: 2px solid #c20427;margin-right:5px; float:left"></a>
		<br>Vince,<br>
		<span style="margin-left:5px"><img src="images/readytowatch.png" alt="readyto.watch" width="140"> founder</span>
		<br><br><br>
		<h2>Contact Us</h2>
		<p>
			Questions, concerns, suggestions? Contact us at <a href="mailto:support@readyto.watch" target="_top"><b>support@readyto.watch</b></a>... Or just tell us here:
		</p>
		<form role="form" id="feedback-form" action="" method="post" enctype='multipart/form-data'>
			<textarea class="form-control" rows="3"></textarea>
			<button class="btn btn-primary btn-lg" disabled="disabled" type="submit" value="Send feedback">Send feedback</button>
			<span style="margin-left:8px" class="message-success"></span>
		</form>
	</div>

	<div class="left-nav social-media">
		<h3>Find us on:</h3>
		<a href="https://www.facebook.com/pages/readytowatch/312702495577187" target="_blank">
			<div class="social-icon">
				<i class="fa fa-facebook"></i>
			</div>
		</a>
		<a href="https://twitter.com/readytowatch" target="_blank">
			<div class="social-icon" target="_blank">
				<i class="fa fa-twitter"></i>
			</div>
		</a>
		<a href="https://plus.google.com/101109367661376865300/about" target="_blank">
			<div class="social-icon">
				<i class="fa fa-google-plus"></i>
			</div>
		</a>
	</div>

</div> <!-- #container -->

<?php
$addedScripts = "<script>
$( window ).scroll( function () {
	if ($( this ).scrollTop() > 0) {
		$('.logo-header').addClass('smaller');
	} else {
		$('.logo-header').removeClass('smaller');
	}
});





</script>";






gen_page_footer($addedScripts); ?>