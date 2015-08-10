<?php // signup
include 'includes/functions.php';
include 'includes/mysqli_connect.php';
include 'includes/login_check.php';
include 'includes/track_page_view.php';

if (isset($_SESSION['user']))
  header('Location: account');

$prevUser = (isset($_SESSION['user_prev']) ? $_SESSION['user_prev'] : "");

gen_page_header('Sign Up | readyto.watch');

?>

<body>
<?php
require_once 'includes/navbar.php';

include 'includes/left_navbar.php';
?>
  <div id="container">
    <div class="jumbotron text-center login-jumbotron box" style="padding: 10px 0 40px">
        <h1>Welcome.</h1>
          <button type="button" class="btn btn-default help-popover-btn" data-container="body" data-html="true" data-toggle="popover" data-placement="bottom" 
            title="<b>Why should I sign up?</b>" 
            data-content="<p>By signing up, you get to customize what kind of search results you'll get (i.e. what services we display), and set alerts for when movies become available to stream. It also lets us 
            track what kind of stuff you like so we can offer suggestions to you later.</p>" style="right: 4%">
            <i class="fa fa-lightbulb-o" style="color: #c20427; margin-right: 5px; font-size: 18px"></i> Why should I sign up?
          </button>
        <p>Please join us, so we can personalize your browsing experience. It's free!</p>
          <div class="well">
            <a href="FacebookSDK/fbconfig.php"><div class="btn btn-primary fb-login" style="width: auto"><i class="fa fa-facebook-official"></i> Sign up with Facebook</div></a>
            <a class="reveal-elem-trigger"><h4><img src="images/logo_small.png" height="30" />Sign up manually <i class="fa fa-caret-down"></i></h4></a>

                  <!-- ~~~~~~~~~~~~ REGISTER FORM ~~~~~~~~~~~~~~~~~ -->
                <form role="form" method="post" action="authenticate.php" id="p-reg-form" class="revealed-elem">
                    
                    <div class="form-group has-feedback">
                      <label for="reg-username">Username</label>
                      <input type="text" class="form-control" id="reg-username" name="reg-user" placeholder="Username">
                      <span class='form-control-feedback'><i class='fa fa-lg'></i></span>
                      <span class='reg-warning' id='reg-username-warning'></span>
                    </div>
                    <div class="form-group has-feedback">
                      <label for="reg-email">Email Address</label>
                      <input type="email" class="form-control" id="reg-email" name="reg-email" placeholder="Enter email">
                      <span class='form-control-feedback'><i class='fa fa-lg'></i></span>
                      <span class='reg-warning' id='reg-email-warning'></span>
                    </div>
                    <div class="form-group has-feedback">
                      <label for="reg-email-conf">Confirm Email Address</label>
                      <input type="email" class="form-control" id="reg-email-conf" placeholder="Confirm email">
                      <span class='form-control-feedback'><i class='fa fa-lg'></i></span>
                      <span class='reg-warning' id='reg-email-conf-warning'></span>
                    </div>
                    <div class="form-group has-feedback">
                      <label for="reg-password">Password</label>
                      <input type="password" class="form-control" id="reg-password" name="reg-password" placeholder="Password">
                      <span class='form-control-feedback'><i class='fa fa-lg'></i></span>
                      <div class="help-block">Your password must be at least 6 characters.</div>
                      <span class='reg-warning' id='reg-password-warning'></span>
                    <button type="submit" class="btn btn-primary" id="p-button-submit">Sign up!</button>
                    <div id="p-reg-success-message"></div>
                </form>
                <!-- </div> --><!-- /form -->

              <!-- ~~~~~~~~~~~~ REGISTER FORM ~~~~~~~~~~~~~~~~~ -->

          </div>
    </div>
  </div> <!-- #container -->

<?php

gen_page_footer("<script>$( document ).ready( function () { $( '#reg-username' ).focus(); } );</script>");

?>