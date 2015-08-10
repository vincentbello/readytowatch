<?php //search
include 'includes/mysqli_connect.php';

include 'includes/functions.php';

include 'includes/login_check.php';
if (!$loggedIn)
	header('Location: login');

include 'includes/track_page_view.php';

gen_page_header('My Alerts | readyto.watch');
?>

<body>

<?php
include 'includes/navbar.php';

$navbarPage = 9;
include 'includes/left_navbar.php';

?>
<div id="container">
	<button type="button" class="btn btn-default wrong-link" data-container="body" data-html="true" data-toggle="popover" data-placement="bottom" 
		title="<b>Setting an alert</b>" 
		data-content="<p>Movies are frequently added to the iTunes Store, Netflix, etc. If you want us to email you when a movie is available, then set alerts on the movie's page or in search results.</p>">
  		<i class="fa fa-lightbulb-o" style="color: #c20427; margin-right: 5px; font-size: 18px"></i> What are alerts?
	</button>
	<h1>My Alerts</h1>

	<?php
	
	$q = $mysqli->query("SELECT * FROM alerts WHERE user_id={$account['id']} AND any+itunes+amazon+netflix+youtube+crackle+google_play>0");

	$total = $q->num_rows;
	if ($total > 0) {
	echo "You <span id='alerts-count'>have <b>" . (($total == 1) ? "1</b> alert set up</span>." : "$total</b> alerts set up</span>.");
	?>
	<br>Alerts will be sent to <b><?php echo $account['email'] ?></b>.
	<form role="form" method="post" id="alerts-form" onsubmit="return false">
		<table class="table table-striped table-alerts box">
			<thead>
				<tr>
					<th></th>
					<th>Movie (Year)</th>
					<th>Any</th>
					<th><i class="fa fa-apple" data-toggle="tooltip" data-placement="top" title="iTunes Store"></i> iTunes</th>
					<th><img src="images/amazon_small.png" data-toggle="tooltip" data-placement="top" title="Amazon Instant Video">Amazon</th>
					<th><img src="images/netflix_small.png" data-toggle="tooltip" data-placement="top" title="Netflix">Netflix</th>
					<th><i class="fa fa-youtube-play" data-toggle="tooltip" data-placement="top" title="YouTube Movies"></i> YouTube</th>
					<th><img src="images/crackle_small.png" data-toggle="tooltip" data-placement="top" title="Crackle">Crackle</th>
					<th style="font-size: 75%"><i class="fa fa-play" data-toggle="tooltip" data-placement="top" title="Google Play Store"></i> Google Play</th>
				</tr>
			</thead>
			<tbody>
				<?php
				
				if($q) {
					while($alert = mysqli_fetch_assoc($q)) {
						$movie = mysqli_fetch_assoc($mysqli->query("SELECT title, year FROM movies WHERE id=" . $alert['id']));
						?>
						<tr class="mov-alert" id="a<?php echo $alert['id'] ?>">
							<td><i data-toggle="tooltip" data-placement="top" title="Remove alert" id="del-alert-<?php echo $alert['id'] ?>" class="fa fa-times-circle"></i></td>
							<td><?php echo "<a href='movie/" . $alert['id'] . "/". rewriteUrl($movie['title']) ."'>" . $movie['title'] . "</a> (" . $movie['year'] . ")"; ?></td>
							<td><div class="form-group"><input type="checkbox" name="any"<?php if ($alert['any'] == 1) echo ' checked'?>></div></td>
							<td><div class="form-group"><input type="checkbox" name="itunes"<?php if ($alert['itunes'] == 1) echo ' checked'?>></div></td>
							<td><div class="form-group"><input type="checkbox" name="amazon"<?php if ($alert['amazon'] == 1) echo ' checked'?>></div></td>
							<td><div class="form-group"><input type="checkbox" name="netflix"<?php if ($alert['netflix'] == 1) echo ' checked'?>></div></td>
							<td><div class="form-group"><input type="checkbox" name="youtube"<?php if ($alert['youtube'] == 1) echo ' checked'?>></div></td>
							<td><div class="form-group"><input type="checkbox" name="crackle"<?php if ($alert['crackle'] == 1) echo ' checked'?>></div></td>
							<td><div class="form-group"><input type="checkbox" name="google_play"<?php if ($alert['google_play'] == 1) echo ' checked'?>></div></td>
						</tr>		
					<?php
					}
				}
				?>
			</tbody>
		</table>
		<button type="submit" id="alerts-submit" class="btn btn-primary btn-lg disabled">Save changes</button>
		<span class='message-success' style="display:none; margin-left: 8px">
			<span class="loading" style="display:none"><i class="fa fa-circle-o-notch fa-spin"></i></span>
			<i class='fa fa-check-circle'></i> OK! Your changes have been saved.
		</span>
	</form>
	<?php
	} else {
		echo "You don't have any alerts set up.";
		echo "<br>Alerts will be sent to <b>" . $account['email'] . "</b>.";
	}

// close the connection
$mysqli->close();
?>

</div> <!-- #container -->

<?php

$addedScripts = "<script src=\"js/bootstrap/bootstrap-switch.js\"></script>
				<script>
					$(\"#alerts-form input[type='checkbox']\").bootstrapSwitch();
				</script>";

gen_page_footer($addedScripts);

?>