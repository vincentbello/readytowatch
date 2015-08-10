  <?php //includes/left_navbar.php ?>
<div id="left-navbar">
<ul class="nav nav-pills" style="width: 221px">
  <li<?php if ($navbarPage == 1) echo ' class="active"'?>><a href="http://www.readyto.watch"><i class="fa fa-home"></i> Home</a></li>
  <li<?php if ($navbarPage == 2) echo ' class="active"'?>><a class="return-to-search"><i class="fa fa-search"></i> Search</a></li>
  <li<?php if ($navbarPage == 5) echo ' class="active"'?>><a href="http://www.readyto.watch/search/filter"><i class="fa fa-filter"></i> Filter Search</a></li>
  <li class="navbar-divider" <?php if ($navbarPage == 4) echo ' class="active"'?>><a href="http://www.readyto.watch/history"><i class="fa fa-clock-o"></i> History</a></li>

  <li<?php if ($navbarPage == 3) echo ' class="active"'?>>
  	<?php echo (($loggedIn) ? "<a href='http://www.readyto.watch/account'><i class='fa fa-user'></i> Account</a>" : "<a href='http://www.readyto.watch/login'><i class='fa fa-sign-in'></i> Log In</a>") ?>
  </li>
    <?php if ($loggedIn) echo "<li".(($navbarPage == 6) ? " class='active'" : "" ) . "><a href='http://www.readyto.watch/favorites'><i class='fa fa-star'></i> Favorites</a></li>
                                                                   <li".(($navbarPage == 9) ? " class='active'" : "" ) . "><a href='http://www.readyto.watch/alerts'><i class='fa fa-bell'></i> Alerts</a></li>"; ?>
  <li class="navbar-divider">
  	<?php echo (($loggedIn) ? "
  		<a href='logout.php'><i class='fa fa-sign-out'></i> Log Out</a>" : 
  	"<a href='http://www.readyto.watch/signup'><i class='fa fa-pencil-square-o'></i> Sign Up</a>") ?>
  </li>
  <li<?php if ($navbarPage == 7) echo ' class="active"'?>><a href="http://www.readyto.watch/about"><i class="fa fa-info-circle"></i> About Us</a></li>
  <li><a href="https://www.facebook.com/pages/readytowatch/312702495577187" target="_blank"><i class="fa fa-facebook"></i> Like Us</a></li>
  <li><a href="https://twitter.com/readytowatch" target="_blank"><i class="fa fa-twitter"></i> Follow Us</a></li>
  <li><a href="https://plus.google.com/101109367661376865300/about" target="_blank"><i class="fa fa-google-plus"></i> Join Us</a></li>
</ul>
</div>
