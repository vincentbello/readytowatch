<?php //check if someone just registered.
if (isset($_POST['feedback'])) {
	session_start();
	$user = "Anonymous";
	if ($_SESSION['user'])
		$user = $_SESSION['user'];
	else if ($_SESSION['fbId'])
		$user = $_SESSION['fbId'];
	$email = "vince@readyto.watch";

	$subject = "New feedback from $user";
	$message = "
	<div style='background:white;width:90%;padding:2%;margin:0 auto;border-radius: 4px; border:20px solid #e3e3e3; font-size:16px; font-family: \"HelveticaNeue-Light\",\"Helvetica Neue Light\", \"Helvetica Neue\", Helvetica, Arial, sans-serif;'>
	<a href='http://www.readyto.watch/'><img src='http://www.readyto.watch/images/readytowatch.png' height='40' style='margin:5px'></a>
	<p>You have new feedback from $user:</p>
	<p>".strip_tags($_POST['feedback'])."
	</div>";
	
	$headers = "From: readyto.watch\r\n";
	$headers.= "MIME-Version: 1.0\r\n";
	$headers.= "Content-Type: text/html; charset=ISO-8859-1\r\n";
	mail($email, $subject, $message, $headers);
}

?>