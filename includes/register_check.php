<?php //check if someone just registered.
if (isset($_POST['reg-user'])) {
	$user = strip_tags($_POST['reg-user']);
	$email = strip_tags($_POST['reg-email']);
	$password = hash('sha256', strip_tags($_POST['reg-password']));
	$hash = md5( rand(0, 1000) );
	mysqli_query($mysqli, "INSERT INTO users VALUES (NULL, '$user', '$password', NULL, NULL, NULL, '$email', 'images/no_user_image.png', 0, 1, 1, '$hash', '', '', 0)");

	$subject = 'Welcome to readyto.watch!';
	$message = "
	<div style='background:white;width:90%;padding:2%;margin:0 auto;border-radius: 4px; border:20px solid #e3e3e3; font-size:16px; font-family: \"HelveticaNeue-Light\",\"Helvetica Neue Light\", \"Helvetica Neue\", Helvetica, Arial, sans-serif;'>
	<a href='http://www.readyto.watch/'><img src='http://www.readyto.watch/images/readytowatch.png' height='40' style='margin:5px'></a>
	<p>Hi <span style='font-weight:bold;color:#c20427'>$user</span>,<br><br>
	Thanks for signing up!<br>
	Please use the following link to verify your email address:</p>
	<a style='text-decoration:none' href='http://www.readyto.watch/verify.php?email=$email&hash=$hash'>
	<div style='color:white;border-radius:4px;margin-top:8px;width:130px;font-size:18px;background-image:-webkit-linear-gradient(top, #c20427 0, #86031b 100%);background-image: linear-gradient(to bottom, #c20427 0, #86031b 100%);border-color: #7c0319;padding:8px 12px; text-align:center'>Verify Account</button>
	</a>
	<br>Now go find the movie you want!<br>
	</div>";
	
	$headers = "From:support-noreply@readyto.watch\r\n";
	$headers.= "MIME-Version: 1.0\r\n";
	$headers.= "Content-Type: text/html; charset=ISO-8859-1\r\n";
	mail($email, $subject, $message, $headers);
}

?>