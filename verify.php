<?php // signup
include 'includes/functions.php';
include 'includes/mysqli_connect.php';
include 'includes/login_check.php';
include 'includes/track_page_view.php';
include 'includes/verify_check.php';

gen_page_header('Verify | readyto.watch');
?>

<body>
<?php
require_once 'includes/navbar.php';

include 'includes/left_navbar.php';
?>
<div id="container" style="text-align:center">
  <?php
  if ($match > 0) {
  ?>
  <h1>Thank you!</h1>
  <p class="success-message well">
    <i class="fa fa-check-circle fa-2x"></i>Awesome, your email has been verified! You can now log in.
  </p>
<div class="login-jumbotron" style="margin-top:10px">
  <div class="well">
   <form role="form" id="p-form" action="account" method="post">
     <div class="form-group">
       <label for="p-username">Username</label>
       <input type="text" class="form-control" id="p-username" placeholder="Enter username" name="login_username" value="<?php echo $username ?>">
     </div>
     <div class="form-group">
       <label for="p-password">Password</label>
       <input type="password" class="form-control" id="p-password" placeholder="Password" name="login_password">
     </div>
     <div class="checkbox">
       <label>
         <input type="checkbox" name='stay-logged'> Keep me logged in
       </label>
     </div>
     <button type="submit" class="btn btn-primary" id="p-button-submit">Log in!</button>
     <div id="p-message"></div>
     <div style="width: 100%; text-align: center">
       <a href="/"><b>Forgot your password?</b></a>
     </div>
   </form>
  </div>
</div>

  <?php
  } else {
  ?>
  <p class="success-message well">
    <i class="fa fa-question-circle fa-2x" style="color: #c20427"></i>Hmm, how did you get here?<br>Please click the link in the email that was sent to you.
  </p>






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
<?php
if ($match > 0) {
  ?>

<script>
  // checks login input via ajax
$("#p-form").submit(function (e) {
  e.preventDefault();
  var form = this;
  var user = $("#p-username").val();
  $.ajax({
    type: "POST",
    url: "preLogin.php",
    data: { username: user, password: $("#p-password").val() }
  }).done(function(result) {
    debugger;
      if (result.length == 0) {
        $("#p-message").html("<span class='label label-success' style='font-size: 100%; margin-bottom: 15px'><i class='fa fa-check'></i> Welcome, " + user + "!</span>");
        setTimeout(function () {
          form.submit();
        }, 1000);
      } else {
        $("#p-message").append(result);
        if (result.indexOf("password") != -1) { // if password is incorrect
          $("#p-password").focus();
          $("#p-password").keydown(function () { $("#p-message").empty() });
        } else {
          $("#p-username").focus();
          $("#p-username").keydown(function () { $("#p-message").empty() });
        }
      }
  }).fail(function() {
    alert("ERROR");
  });
});
</script>
<?php
}

?>

</body>
</html>