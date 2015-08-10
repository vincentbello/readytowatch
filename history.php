<?php
include 'includes/functions.php';
include 'includes/mysqli_connect.php';
include 'includes/login_check.php';
include 'includes/track_page_view.php';
gen_page_header('Search History | readyto.watch');
?>

<body>
<?php
include 'includes/navbar.php';

$page = 4;
include 'includes/left_navbar.php';
?>
<div id="container">
<h1>History</h1>
<p>View up to 30 of your most recent searches here.</p>
  <?php
  if ($loggedIn) {
    date_default_timezone_set("America/New_York");
    $i = 0;
    $query = "SELECT * FROM history WHERE user_id={$account['id']} ORDER BY timestamp DESC";
    if ($t = $mysqli->query($query)) {
      echo "<ul class='history box'>";
      while (($result = mysqli_fetch_assoc($t)) && ($i < 30)) { // if we have history for this user
        echo "<li><a href='search?q=". urlencode($result['search']) ."'><b>" . $result['search'] . 
        "</b></a><span class='history-time'> - " . gen_time_ago($result['timestamp']);
        echo "</span></li>";
        $i++;
      }
      echo "</ul>";
    }
    if ($i == 0)
      echo "<p class='important-message'>Sorry, you have no recent searches. <a class='return-to-search'>Start a search now!</a></p>";
  } else {
    echo "<p class='important-message'>We'll only track your recent searches if you are logged in. <a href='login'>Log in</a> or <a href='signup'>sign up</a>!</p>";
  }
    ?>
</div>

</div> <!-- #container -->

<?php include 'includes/footer.php'; ?>


<script src="js/jquery-1.11.1.min.js"></script>
<script src="js/jquery-ui.min.js"></script>
<script src="js/bootstrap/bootstrap.min.js"></script>
<script src="js/bootstrap/bootstrap3-typeahead-min.js"></script>
<script src="js/custom.js"></script>

</body>
</html>