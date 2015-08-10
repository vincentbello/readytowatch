<?php // login
// for now, assume the user is logged in
include 'includes/functions.php';
include 'includes/mysqli_connect.php';
include 'includes/login_check.php';
include 'includes/track_page_view.php';

if ($loggedIn) {
  header('Location: account');
}

gen_page_header('Reset your password | readyto.watch');

?>

<body>
<?php
require_once 'includes/navbar.php';

include 'includes/left_navbar.php';
?>

  <div id="container">

<?php
if (isset($_GET['email']) && isset($_GET['resetHash'])) {
  if ($mysqli->query("SELECT * FROM users WHERE email='".$_GET['email']."' AND resetHash='".$_GET['resetHash']."'")->num_rows > 0) { // they match
?>
    <div class="jumbotron text-center login-jumbotron" style="padding-top: 10px">
        <p style="font-size: 23px">Your password has been reset. Please choose a new one:</p>
          <div class="well">
            <form role="form" id="reset-form" action="login?r=1" method="post" style="width: 50%; margin: 0 auto; text-align:left">
              <div class="form-group">
                <input type="hidden" name="changedEmail" value="<?php echo $_GET['email'] ?>">
                <label for="reset-pass">New password</label>
                <input type="password" class="form-control" id="reset-pass" placeholder="New password" name="resetPass">
              </div>
              <div class="form-group">
                <label for="reset-pass-confirm">Confirm</label>
                <input type="password" class="form-control" id="reset-pass-confirm" placeholder="Confirm new password">
              </div>
              <button type="submit" class="btn btn-primary" id="reset-button-submit">Change password</button>
              <div id="reset-message" style="color:#c20427"></div>
            </form>
          </div>
        </p>
    </div>
<?php
} else {
  header('Location: resetPass.php');
}
} else if (isset($_POST['email'])) { // we'll send you the email.
  include 'includes/resetPassword.php';
?>
  <h1 style="text-align:center">Thank you!</h1>
  <p class="success-message well"><i class="fa fa-check-circle fa-2x"></i> Click the link we just sent to your email to reset your password. Make sure to check your spam folder!</p>

<?php
} else {
?>
    <div class="jumbotron text-center login-jumbotron" style="padding-top: 10px">
      <h1>Forgot your password?</h1>
        <p style="font-size: 23px">Please enter your registered readyto.watch email and we'll help you reset your password.</p>
          <div class="well">
            <form role="form" id="reset-form" action="" method="post">
              <div class="form-group">
                <input type="text" class="form-control" id="reset-email" placeholder="Email address" name="email">
              </div>
              <button type="submit" class="btn btn-primary" id="reset-button-submit">Continue</button>
              <div id="reset-message" style="color:#c20427"></div>
            </form>
          </div>
        </p>
    </div>
<?php
}
?>

  </div> <!-- #container -->

<?php include 'includes/footer.php'; ?>

<script src="js/jquery-1.11.1.min.js"></script>
<script src="js/jquery-ui.min.js"></script>
<script src="js/bootstrap/bootstrap.min.js"></script>
<script src="js/bootstrap/bootstrap3-typeahead-min.js"></script>
<script src="js/custom.js"></script>
<script>
var reset = $("#reset-email");
$("#reset-form").submit(function (e) {
  e.preventDefault();
  var form = this;
  var email = reset.val();
  if (isValidEmailAddress(email)) {
    form.submit();
  } else {
    $('#reset-message').html("<span class='message-danger'><i class='fa fa-times-circle'></i> This is not a valid email address!</span>");
    reset.select();
  }
});

reset.keyup( function () {
  $('#reset-message').empty();
});

$("#reset-form").submit(function (e) {
  e.preventDefault();
  var form = this;
  var pass = $('#reset-pass').val();
  var confPass = $('#reset-pass-confirm').val();
  if ((pass == confPass) && (pass.length >= 6))
    form.submit();
  else if (pass !== confPass)
    $('#reset-message').html("<span class='message-danger'><i class='fa fa-times-circle'></i> The passwords do not match!</span>");
  else if (pass.length < 6)
    $('#reset-message').html("<span class='message-danger'><i class='fa fa-times-circle'></i> Your password must be at least 6 characters.</span>");
});

$('#reset-pass, #reset-pass-confirm').keyup( function () {
  $('#reset-message').empty();
});
</script>

</body>
</html>