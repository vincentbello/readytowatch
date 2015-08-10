<div id='navbar'>
  <form role="form" id='nav-form' onSubmit='return validate_form(this);' action='search' method='get' enctype='multipart/form-data'>
    <a href="http://www.readyto.watch"><div id="logo"></div></a>
    <span class="navigator"><i class="fa fa-bars fa-lg"></i></span>
    
    <!-- <a href="/"><img id="back" src="images/arrow_left.png"></a> -->
<div class="form-group">
    <input type='text' id='nav-search' name='q' maxlength='40' onclick='resetSearch()' placeholder='Search movies or people' autocomplete="off">
  </div>
    <button type="submit" id="search-submit" onclick="return validateSearch()"><i class="fa fa-search fa-lg" style="font-size:24px"></i></button>
    <span id='r-alert'></span>
  </form>
  
<?php
if (!$loggedIn) {
  $formAction = '';
  if (strpos($_SERVER['PHP_SELF'], 'login') !== false)
    $formAction = 'account';
  ?>
  <div>
    <button id='login-dropdown' class='login-dropdown'>
	   Log In
    </button>
    <a href="http://www.readyto.watch/signup"><button class='login-dropdown reveal' style='margin-right: 1%' id="register-button">
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
    <a href='http://www.readyto.watch/account'>
      <span class='login-dropdown reveal usr' style='height: 25px; margin-right: 1%'>
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
      <div style="width:100%"><i class="fa fa-question-circle" style="color:#c20427"></i> <a href="http://www.readyto.watch/resetPass.php"><b>Forgot your password?</b></a>
        <a href="FacebookSDK/fbconfig.php"><div class="btn btn-primary btn-lg fb-login"type="submit"><i class="fa fa-facebook-official"></i> Log in with Facebook</div></a>
      </div>
    </form>
    <!-- ~~~~~~~~~~~~ BOOTSTRAP FORM ~~~~~~~~~~~~~~~~~ -->
    <?php
}
?>