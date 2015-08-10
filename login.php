<?php // login
// for now, assume the user is logged in
include 'includes/functions.php';
include 'includes/mysqli_connect.php';
include 'includes/login_check.php';
include 'includes/track_page_view.php';
include 'includes/resetPasswordCheck.php';

if ($loggedIn) {
  header('Location: account');
}

$prevUser = (isset($_SESSION['user_prev']) ? $_SESSION['user_prev'] : "");

gen_page_header('Log In | readyto.watch');

?>

<body>
<?php
require_once 'includes/navbar.php';

include 'includes/left_navbar.php';
?>
  <div id="container">
    <div class="jumbotron text-center login-jumbotron box" style="padding-top: 10px">
        <h1>Hello again.</h1>
        <p>
          <?php
          if ($resetPass)
            echo 'You can now log in with your new password.';
          else
            echo 'Please log in, just so you feel right at home.';
          ?>
        </p>
          <div class="well">
            <form role="form" id="p-form" action="/" method="post">
              <a href="FacebookSDK/fbconfig.php"><div class="btn btn-primary fb-login"><i class="fa fa-facebook-official"></i> Log in with Facebook</div></a>
              <h4><img src="images/logo_small.png" height="30" /> Log in with readyto.watch</h4>
              <div class="form-group">
                <label for="p-username">Username</label>
                <input type="text" class="form-control" id="p-username" placeholder="Enter username" name="full_login_username" value="<?php echo $prevUser ?>">
              </div>
              <div class="form-group">
                <label for="p-password">Password</label>
                <input type="password" class="form-control" id="p-password" placeholder="Password" name="full_login_password">
              </div>
              <div class="checkbox">
                <label>
                  <input type="checkbox" name='stay-logged-full' checked> Keep me logged in
                </label>
              </div>
              <button type="submit" class="btn btn-primary" id="p-button-submit">Log in!</button>
              <div id="p-message"></div>
              <div style="width: 100%; text-align: center">
                <a href="signup"><b>Sign up</b></a> | 
                <a href="resetPass.php"><b>Forgot your password?</b></a>
              </div>
            </form>
          </div>
        </p>
    </div>
  </div> <!-- #container -->

<?php

gen_page_footer("<script>$( document ).ready( function () { $( '#p-username' ).select(); } );</script>");

?>