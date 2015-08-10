<div id='home-navbar' style='display:table-row;margin-left:0'>

  <span class="navigator" style="margin-left:3%"><i class="fa fa-bars fa-lg"></i></span>
  <a class="return-to-search"><i class="fa fa-search"></i></a>
  <a href="#home"><div id="logo-home" data-toggle="tooltip" data-placement="bottom" title="readyto.watch"></div></a>
  
<?php
if (!$loggedIn) {
  $formAction = '';
  if (strpos($_SERVER['PHP_SELF'], 'login') !== false)
    $formAction = 'account';
  ?>
  <div>
    <button id='login-dropdown' class='login-dropdown reveal'>
     Log In
    </button>
    <a href="signup"><button class='login-dropdown' style='margin-right: 1%' id="register-button">
      Sign Up
    </button></a>
  </div>

<?php
} else {
  $dispName = ($fb) ? $account['fb_fname'] : $account['username'];
  $picture = $account['image'];
?>
  <div>
    <a href="logout.php">
      <span class="login-dropdown">
        Log Out
      </span>
    </a>
    <a href='account'>
      <span class='login-dropdown reveal' style='height: 25px; margin-right: 2.5%'>
        <div>
          <div class="usr-img-box sm">
            <img src="<?php echo $picture ?>">
          </div>
          <?php echo (strlen($dispName) > 15) ? substr($dispName, 0, 15) . "..." : $dispName; ?>
        </div>
      </span>
    </a>
  </div>
<?php
}
?>
</div>
<?php
if (!$loggedIn) {
  ?>
      <!-- ~~~~~~~~~~~~ BOOTSTRAP FORM ~~~~~~~~~~~~~~~~~ -->
    <form role="form" id="login-form" action="<?php echo $formAction ?>" method="post">
      <svg id="dropdown-triangle" height="10" width="18">
        <polygon points="0,10 18,10 9,0" style="fill:white" />
        <line x1="0" y1="10" x2="9" y2="0" />
        <line x1="18" y1="10" x2="9" y2="0" />
      </svg>
      <div class="form-group">
        <label for="login-username">Username</label>
        <input type="text" class="form-control" id="login-username" placeholder="Enter username" name="login_username">
      </div>
      <div class="form-group">
        <label for="login-password">Password</label>
        <input type="password" class="form-control" id="login-password" placeholder="Password" name="login_password">
      </div>
      <div class="checkbox">
        <label>
          <input type="checkbox" name='stay-logged' checked> Keep me logged in
        </label>
      </div>
      <button type="submit" class="btn btn-primary btn-lg" style="margin-top:5px">Log in!</button>
      <div id="login-message"></div>
      <div><i class="fa fa-question-circle" style="color:#c20427"></i> <a href="resetPass.php"><b>Forgot your password?</b></a></div>
      <a href="FacebookSDK/fbconfig.php"><div class="btn btn-primary btn-lg fb-login"type="submit"><i class="fa fa-facebook-official"></i> Log in with Facebook</div></a>
    </form>
    <!-- ~~~~~~~~~~~~ BOOTSTRAP FORM ~~~~~~~~~~~~~~~~~ -->
    <?php
}
?>