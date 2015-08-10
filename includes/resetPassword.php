<?php //assume we just have $_POST['email'] (and a $mysqli connection)
$email = $_POST['email'];
$hash = md5( rand(0, 1000) );

mysqli_query($mysqli, "UPDATE users SET resetHash='$hash' WHERE email='$email'");
$subject = 'Reset your readyto.watch password';
$message = "
<div style='background:white;width:90%;padding:2%;margin:0 auto;border-radius: 4px; border:20px solid #e3e3e3; font-size:16px; font-family: \"HelveticaNeue-Light\",\"Helvetica Neue Light\", \"Helvetica Neue\", Helvetica, Arial, sans-serif;'>
<a href='http://www.readyto.watch/'><img src='http://www.readyto.watch/images/readytowatch.png' height='40' style='margin:5px'></a>
<p>Please click the following link to reset your password:</p>
<a style='text-decoration:none' href='http://www.readyto.watch/resetPass.php?email=$email&resetHash=$hash'>
<div style='color:white;border-radius:4px;margin-top:8px;width:130px;font-size:18px;background-image:-webkit-linear-gradient(top, #c20427 0, #86031b 100%);background-image: linear-gradient(to bottom, #c20427 0, #86031b 100%);border-color: #7c0319;padding:8px 12px; text-align:center'>Reset Password</button>
</a>
</div>";

$headers = "From:support-noreply@readyto.watch\r\n";
$headers.= "MIME-Version: 1.0\r\n";
$headers.= "Content-Type: text/html; charset=ISO-8859-1\r\n";
mail($email, $subject, $message, $headers);

?>