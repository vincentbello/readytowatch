<?php // signup
if (!isset($_POST['reg-user'])) {
  header('Location: account');
}

include 'includes/functions.php';
include 'includes/mysqli_connect.php';
include 'includes/login_check.php';
include 'includes/track_page_view.php';
include 'includes/register_check.php';

gen_page_header('Authenticate | readyto.watch');

?>

<body>
<?php
require_once 'includes/navbar.php';

include 'includes/left_navbar.php';
?>
  <div id="container" style="text-align:center">
        <h1>Thank you!</h1>
          <button type="button" class="btn btn-default help-popover-btn" data-container="body" data-html="true" data-toggle="popover" data-placement="bottom" 
            title="<b>Account authentication</b>" 
            data-content="<p>By authenticating your email, we can make sure you are a real user, and not a bot.</p>">
            <i class="fa fa-lightbulb-o" style="color: #c20427; margin-right: 5px; font-size: 18px"></i> Why send me an email?
          </button>
        <p class="success-message well"><i class="fa fa-rocket fa-2x"></i> You're almost there! Click the verification link we just sent to your email and you'll be up and running in no time. (Make sure to check your spam folder!)</p>
  </div> <!-- #container -->

<?php include 'includes/footer.php'; ?>

<script src="js/jquery-1.11.1.min.js"></script>
<script src="js/jquery-ui.min.js"></script>
<script src="js/bootstrap/bootstrap.min.js"></script>
<script src="js/bootstrap/bootstrap3-typeahead-min.js"></script>
<script src="js/custom.js"></script>

</body>
</html>