<?php // account
include 'includes/functions.php';
include 'includes/mysqli_connect.php';
include 'includes/login_check.php';
include 'includes/register_check.php';
if (!$loggedIn) {
  header('Location: login');
}
include 'includes/track_page_view.php';

$fullName = $fb ? $account['fb_fname'] . ' ' . $account['fb_lname'] : $account['username'];
if (strpos($account['image'], '//graph.facebook.com') !== false) {
  $account['image'] .= '?width=550';
}
$signedUpQuery = $mysqli->query("SELECT timestamp FROM session_history WHERE user_id={$account['id']} AND (type='fb_signup' OR type='signup')");
$signedUp = mysqli_fetch_assoc($signedUpQuery);
$signedUpTimestamp = ($signedUp) ? date('F j, Y', strtotime($signedUp['timestamp'])) : false;

$lastLoggedInQuery = $mysqli->query("SELECT timestamp, type FROM session_history WHERE user_id={$account['id']} AND (type='fb_login' OR type='login') ORDER BY timestamp DESC LIMIT 1");
$lastLoggedIn = mysqli_fetch_assoc($lastLoggedInQuery);
$lastLoggedInMessage = ($lastLoggedIn) ? "Last logged in" . (($lastLoggedIn['type'] == 'fb_login') ? " (Facebook)" : "") . ": " . "<span class='timestamp' data-timestamp='" . strtotime($lastLoggedIn['timestamp']) . "'" : false;

if (isset($_GET['error'])) {
  if ($_GET['error'] == '1') {
    $error = "<span class='message-danger'><i class='fa fa-times-circle'></i> This Facebook account is already linked to a user. Log out above, then log in with Facebook.</span><br>";
  }

}

$addedStyle = "<style>
  .tab-pane { margin-left: 18% }
  .form-control { width: 70%; float: right;}
  label { position: relative; top: 10px; font-weight:200; }
  label i { margin-right: 5px; }
  h3 { padding-bottom: 10px; font-size: 30px; margin-top: 5px; border-bottom: 1px solid #e4e4e4; }
  .prefs { width: 35%; top:3px;}
  .label { font-size: 110%; }
  #password-change { width: 70%; float: right; }
</style>";

gen_page_header('Account Settings | readyto.watch', $addedStyle);
?>

<body>
<?php
include 'includes/navbar.php';
$navbarPage = 3;
include 'includes/left_navbar.php';

?>

  <div id="container">
    <div class='account-nav-left'>
      <ul class="nav nav-pills nav-stacked">
        <li class="active"><a href="#accountsettings" data-toggle="tab">Account settings</a></li>
        <li><a href="#movieprefs" data-toggle="tab">Movie preferences</a></li>
      </ul>
    </div>


    <div class="modal fade" id="imageModal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
            <h4 class="modal-title"><b><?php echo $fullName ?></b></h4>
          </div>
          <div class="modal-body">
            <div class="usr-img-box lg">
              <img src="<?php echo $account['image'] ?>">
            </div>
        </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <div class="modal fade" id="deleteAccountModal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
            <h4 class="modal-title">Delete account</h4>
          </div>
            <div class="modal-body">
              <p>
                Are you sure you would like to delete your account? All your data, favorites, and alerts will be permanently deleted.
              </p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            <a href="deleteAccount.php"><div class="btn btn-primary">Yes, delete my account</div></a>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->


    <!-- Tab panes -->
    <div class="tab-content">
      <div class="tab-pane fade in active box" id="accountsettings">
        <h3><?php echo $fullName ?>&nbsp;
          <span class="info-right">
            <?php
            if ($signedUpTimestamp)
              echo "Signed up on $signedUpTimestamp<br>";
            if ($lastLoggedInMessage)
              echo "$lastLoggedInMessage<br>";
            ?>
          </span>
        </h3>
        <?php if ($error) echo $error ?>
        <br>
        <div style="overflow:hidden; margin-bottom: 15px">
          <div style="width:18.5%; float: left">
            <div class="usr-img-box-main">
              <img src="<?php echo $account['image'] ?>" data-toggle="modal" data-target="#imageModal">
            </div>
            <div style="margin-top:5px"><i class="fa fa-camera"></i> Profile photo</div>
          </div>

        <form id="usr-img-form" method="post" onsubmit="return false" enctype="multipart/form-data" style="width:70%; float: left">
          <div class="form-group-account">
            <label for="usr-img-upload">Want to change your profile photo?</label>
            <input type="file" id="usr-img-upload">
            <button id="uploadbutton" class="btn btn-primary">Upload a new photo</button>
              <p class="help-block">Your image must be less than 2 MB, and be in JPG, PNG or GIF format.</p>
  
              <span id="response" style="position: absolute;margin: -10px 0 0 5px"></span>
          </div>
  
        </form>
        </div>









        <form role="form" onsubmit="return false" style="margin-bottom:20px">

          <?php if ($fb) { ?>
            <div class="form-group-account">
              <label><i class="fa fa-tag fa-lg"></i> First name</label>
              <input type="text" class="form-control" value="<?php echo $account['fb_fname'] ?>" disabled>
            </div>
            <div class="form-group-account">
              <label><i class="fa fa-bookmark fa-lg"></i> Last name</label>
              <input type="text" class="form-control" value="<?php echo $account['fb_lname'] ?>" disabled>
            </div>
          <?php }
          if ($user) { ?>
            <div class="form-group-account">
              <label><i class="fa fa-user fa-lg"></i> Username</label>
              <input type="text" class="form-control" id="username" value="<?php echo $account['username'] ?>" disabled>
            </div>
          <?php }
          if ($fb && (strlen($account['email']) == 0)) {
          ?>
            <div class="form-group-account">
              <label><i class="fa fa-envelope fa-lg"></i> Email</label>
              
              <div class="form-group-content">
                <a class="reveal-elem-trigger"><b>+ Add an email address&nbsp;&nbsp;<i class="fa fa-caret-down"></i></b></a><br>

                <div class="revealed-elem">
                  <p class="help-block">
                    If you add your email address, we can send you alerts and let you know when your favorite movies are available for streaming.
                  </p>
                  <input type="email" class="form-control" id="new-email">
                </div>
              </div>
              
            </div>         


            <?php
          } else { // No Facebook, or Facebook and an email
            ?>
            <div class="form-group-account">
              <label><i class="fa fa-envelope fa-lg"></i> Email</label>
              <input type="email" class="form-control" id="email" value="<?php echo $account['email'] ?>" disabled>
            </div>
          <?php }
            if (!$fb) { ?>
              <div class="form-group-account">
                <label><i class="fa fa-lock fa-lg"></i> Password 
                  <i id="edit-password" class="fa fa-pencil fa-lg" style="margin-left: 7px; cursor: pointer;" 
                  data-toggle="tooltip" data-placement="top" title="Change password"></i>
                </label>
                <input type="password" class="form-control" id="password" value="xxxxxxxxxxxxxxx" disabled>
                <div id="password-change" style="display:none">
                  <div class="form-group-account" style="width: 100%">
                    <label>Current:</label>
                    <input type="password" class="form-control" id="current-pass" placeholder="Current password">
                  </div>
                  <div class="form-group-account" style="width: 100%">
                    <label>New:</label>
                    <input type="password" class="form-control" id="new-pass" placeholder="New password">
                  </div>
                  <div class="form-group-account" style="width: 100%">
                    <label>Confirm:</label>
                    <input type="password" class="form-control" id="confirm-pass" placeholder="Re-type new password">
                  </div>
                </div> <!-- #password-change -->
              </div> <!-- .form-group-account -->
              
              <div class="form-group-account">
                <label>
                  <i class="fa fa-link fa-lg"></i> Link with Facebook
                </label>
                <div class="form-group-content">
                  <a href="FacebookSDK/fbconfig.php?userid=<?php echo $account['id'] ?>"><div class="btn btn-primary fb-login"><i class="fa fa-facebook-official"></i> Log in with Facebook</div></a>
                  <p class="help-block">
                    By linking your account with Facebook, you can log in almost instantaneously.
                  </p>
                </div>
              </div> <!-- .form-group-account -->
            <?php } ?>
          <button type="submit" id="settings-submit" class="btn btn-primary btn-lg disabled">Save changes</button>
          <span id="pass-save-message" style='margin-left: 15px; display: inline'></span>
        </form>
        <div><a data-toggle="modal" data-target="#deleteAccountModal"><b>Delete my account</b></a></div>
      </div> <!-- .tab-content -->

      <div class="tab-pane fade box" id="movieprefs">
        <h3><b>Movie Preferences</b></h3><br>
        <form role="form" onsubmit="return false">
          <li><div class="form-group-account">
            <label class="prefs">Show adult movies?</label>
            <input type="checkbox" name="adult"
            <?php if ($account['adult'] == 1) echo " checked" ?>>
          </div></li>
          <li><div class="form-group-account">
            <label class="prefs">Show Amazon Prime results?</label>
            <input type="checkbox" name="amazon"
            <?php if ($account['amazon_prime'] == 1) echo " checked" ?>>
          </div></li>
          <li><div class="form-group-account">
            <label class="prefs">Show Netflix results?</label>
            <input type="checkbox" name="netflix"
            <?php if ($account['netflix'] == 1) echo " checked" ?>>
          </div></li>
          <button type="submit" id="movieprefs-submit" class="btn btn-primary btn-lg disabled">Save changes</button>
          <span class="message-success" id="save-message" style='margin-left: 15px'></span>
        </form>
      </div>
    </div> <!-- .tab-content -->
  

  </div> <!-- #container -->

<?php

  $addedScripts = "<script src=\"js/date.js\"></script>
  <script src=\"js/bootstrap/bootstrap-switch.js\"></script>
  <script>
  $(\"#movieprefs [type='checkbox']\").bootstrapSwitch();
  $(\"#movieprefs [type='checkbox']\").on('switchChange.bootstrapSwitch', function(event, state) {
    $(\"#movieprefs-submit\").removeClass(\"disabled\"); // DOM element
  });
  </script>";
  
  gen_page_footer($addedScripts);

?>