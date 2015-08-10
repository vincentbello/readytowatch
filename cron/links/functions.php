<?php

function send_new_link_email($email, $username, $id, $title, $linkType) {

	$subject = 'New readyto.watch link!';
	$message = "
	<div style='background:white;width:90%;padding:2%;margin:0 auto;border-radius: 4px; border:20px solid #e3e3e3; font-size:16px; font-family: \"HelveticaNeue-Light\",\"Helvetica Neue Light\", \"Helvetica Neue\", Helvetica, Arial, sans-serif;'>
	<a href='http://www.readyto.watch/'><img src='http://www.readyto.watch/images/readytowatch.png' height='40' style='margin:5px'></a>
	<p>Hello <span style='font-weight:bold;color:#c20427'>$username</span>,<br><br>
	Good news!<br>
	<b>$title</b> is now available on $linkType. Follow the link below to go to the movie:</p>
	<a style='text-decoration:none' href='http://www.readyto.watch/movie/$id#" . strtolower($linkType) . "' target='_blank'>
	<span style='color:white;border-radius:4px;font-size:18px;background-image:-webkit-linear-gradient(top, #c20427 0, #86031b 100%);background-image: linear-gradient(to bottom, #c20427 0, #86031b 100%);border-color: #7c0319;padding:8px 12px; text-align:center'>$title</span>
	</a>
	<p>Thanks for using <a href='http://www.readyto.watch/'>readyto.watch</a>. Happy watching!</p>
	</div>";
	
	$headers = "From:newlinks-noreply@readyto.watch\r\n";
	$headers.= "MIME-Version: 1.0\r\n";
	$headers.= "Content-Type: text/html; charset=ISO-8859-1\r\n";
	mail($email, $subject, $message, $headers);
}

?>