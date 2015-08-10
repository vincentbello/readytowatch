<?php
include 'includes/functions.php';

include 'includes/mysqli_connect.php';

include 'includes/login_check.php';

include 'includes/track_page_view.php';

$ymax = date("Y");
$ymin = 1950;
//if ($test = mysqli_query($mysqli,"SELECT MIN(NULLIF(year, 0)) FROM movies")) $ymin = mysqli_fetch_assoc($test)['MIN(NULLIF(year, 0))'];

//if ($test = mysqli_query($mysqli,"SELECT MAX(runtime) FROM movies"))
  $min = 0;
  $max = 200;
// $max = mysqli_fetch_assoc($test)['MAX(runtime)'];
//if ($test = mysqli_query($mysqli,"SELECT MIN(NULLIF(runtime, 0)) FROM movies")) $min = mysqli_fetch_assoc($test)['MIN(NULLIF(runtime, 0))'];

$totalMovies = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT COUNT(*) FROM movies WHERE year <= $ymax"))['COUNT(*)'];
$totalMoviesFormatted = number_format($totalMovies);

gen_page_header('Filter Search | readyto.watch', "<link type='text/css' rel='stylesheet' href='css/slider.css' />");
?>

<body>
<?php
include 'includes/navbar.php';

$page = 5;
include 'includes/left_navbar.php';
?>
<div id="container">
  <h1>Filter Search</h1>

  <button type="button" class="btn btn-default help-popover-btn" data-container="body" data-html="true" data-toggle="popover" data-placement="bottom" 
title="<b>About the Filter Search</b>" 
data-content="<p>The Filter Search is still in beta mode, and we are working on improving it and adding more features.</p><p>We eventually want people to be able to use the Filter Search to find interesting movies that they haven't seen yet.</p>">
  <i class="fa fa-lightbulb-o" style="color: #c20427; margin-right: 5px; font-size: 18px"></i> About the Filter Search
</button>


  <div class='cols-wrapper'>
    <div class='col-60' style='width:59%;margin-right:1%'>
      <p>Welcome to the <span class="emphasize">Filter Search</span>, which allows you to refine a search to find just the kind of movies you like.
      <br>Don't have much time? Select movies shorter than 120 minutes. Need a flick for kids? Select animation movies rated G.
      <br>Our library of movies is growing every day &#8211; we're bound to find something for you.</p>
    </div>
    <div class='col-40'>
      <div class='well box' id='match-movies-box'>
        <span class="important-message">We have <span id="match-movies"><b style='color:#c20427'><?php echo number_format($totalMovies) ?> total movies</b>.</span></span>
        <div id="match-progress-container"><div id="match-progress" data-toggle="tooltip" data-placement="bottom" title="100%" ></div></div>
      </div>
    </div>
  </div>
<form action='filter' method='get' enctype='multipart/form-data' id="filter-form">
<div class='box'>

<div class='filter'>
<div class='filter-param'>Year </div>

<input type="text" id="year-min" class="filter-minmax" name="year-min" value="<?php echo "< $ymin"?>">
<!-- <div style="width: 300px; margin: 4px 10px 0 10px; float: left; overflow: hidden;"> -->
<input type="text" class="filter-slider" style="display: none" value="" data-slider-min="<?php echo $ymin?>" data-slider-max="<?php echo $ymax?>" data-slider-step="1" data-slider-value="[<?php echo $ymin . ',' . $ymax?>]" id="slider-year">
<!-- </div> -->
<input type="text" id="year-max" class="filter-minmax" name="year-max" value="<?php echo $ymax?>">
<div class="overflow-alternative"></div>


<!-- <div class='filter-slider'>

<div style="width: 300px; margin: 4px 10px 0 10px; float: left; overflow: hidden;" id="slider-runtime"></div>

</div> -->
</div>

<!-- -NEW~~~~~~~~~~~~~~~~~~~~~~~~
 -->

<div class='filter'>
<div class='filter-param'>Runtime </div>

<input type="text" id="runtime-min" class="filter-minmax" name="runtime-min" value="0 min">
<!-- <div style="width: 300px; margin: 4px 10px 0 10px; float: left; overflow: hidden;"> -->
<input type="text" class="filter-slider" value="" data-slider-min="0" data-slider-max="<?php echo $max?>" data-slider-step="1" data-slider-value="[0,<?php echo $max?>]" id="slider-runtime">
<!-- </div> -->
<input type="text" id="runtime-max" class="filter-minmax" name="runtime-max" value="<?php echo $max?> min+">
<div class="overflow-alternative"></div>
</div>

<div class='filter' id='language-filter'>
<div class='filter-param'>Language </div>
  <input type="hidden" name="language" value="English">
  <button class="btn btn-default dropdown-toggle" data-toggle="dropdown">English (default) <span class="caret"></span></button>
  <ul class="dropdown-menu" role="menu" style="margin-left: 150px; top: auto; left: auto">
    <?php
      $languages = array('Any language', 'Afrikaans', 'Chinese', 'Czech', 'Danish', 'Dutch', 'French', 'English', 'German', 'Greek', 'Hebrew', 'Hungarian', 'Italian', 'Japanese', 'Korean', 'Latin', 'No Language', 'Norwegian', 'Polish', 'Portuguese', 'Russian', 'Spanish', 'Suomi', 'Swedish');
      foreach($languages as $l) {
        echo "<li><a>$l</a></li>";
    }?>
  </ul>
  <div class="overflow-alternative"></div>
</div>

<div class='filter' id='genre-filter'>
<div class='filter-param'>Genre </div>
  <input type="hidden" name="genre" value="Any">
  <button class="btn btn-default dropdown-toggle" data-toggle="dropdown">Any (default) <span class="caret"></span></button>
  <ul class="dropdown-menu" role="menu" style="margin-left: 150px; top: auto; left: auto">
    <?php
    $genres = array("Any", "Action", "Adventure", "Animation", "Comedy", "Crime", "Disaster", "Documentary", "Drama", "Eastern", "Erotic", "Family", "Fantasy", "Film Noir", "Foreign", "History", "Holiday", "Horror", "Indie", "Music", "Musical", "Mystery", "Neo-noir", "Road Movie", "Romance", "Science", "Science Fiction", "Short", "Sports Film", "Suspense", "TV movie", "Thriller", "War", "Western");
    foreach($genres as $g) {
      echo "<li><a>$g</a></li>";
    } ?>
  </ul>
  <div class="overflow-alternative"></div>
</div>

<div class='filter' id='mpaa-filter'>
<div class='filter-param'>MPAA Rating </div>
  <input type="hidden" name="mpaa" value="Any">
  <button class="btn btn-default dropdown-toggle" data-toggle="dropdown">Any (default) <span class="caret"></span></button>
  <ul class="dropdown-menu" role="menu" style="margin-left: 150px; top: auto; left: auto">
    <?php
    $mpaa = array("Any", "G", "PG", "PG-13", "R", "NC-17", "NR", "P", "R18", "X", "M", "TV Movie", "TV-MA");
    foreach($mpaa as $m) {
      echo "<li><a>$m</a></li>";
    } ?>
  </ul>
  <div class="overflow-alternative"></div>
</div>

<div class='filter' style='border-bottom: none' id='orderby-filter'>
<div class='filter-param'>Order by </div>
  <input type="hidden" name="orderby" value="imdb_rating">
  <button class="btn btn-default dropdown-toggle" data-toggle="dropdown">IMDb rating (default) <span class="caret"></span></button>
  <ul class="dropdown-menu" role="menu" style="margin-left: 150px; top: auto; left: auto">
    <?php
    $orders = array("IMDb rating", "Year", "Runtime");
    foreach($orders as $o) {
      echo "<li><a>$o</a></li>";
    } ?>
  </ul>
  <div class="overflow-alternative"></div>
</div>
<!-- -NEW~~~~~~~~~~~~~~~~~~~~~~~~
 -->
</div>
<button type="submit" class="btn btn-primary btn-lg" style="margin: 15px; font-size:23px">Find movies!</button>

</form>

  </div> <!-- #container -->

<?php include 'includes/footer.php'; ?>


<script src="js/jquery-1.11.1.min.js"></script>
<script src="js/jquery-ui.min.js"></script>
<script src="js/bootstrap/bootstrap.min.js"></script>
<script src="js/bootstrap/bootstrap3-typeahead-min.js"></script>
<script src="js/custom.js"></script>
<script src="js/bootstrap/bootstrap-slider.js"></script>

<script>

$( document ).ready( function () {
  $("#slider-year").parent().find(".tooltip-inner").html('<?php echo "< $ymin - $ymax"; ?>');
  $("#slider-runtime").parent().find(".tooltip-inner").html("0 min - <?php echo $max . '+'; ?> min");
});

  var totalMovies = <?php echo $totalMovies ?>;

    $("#slider-runtime").slider()
    .on('slideStop', function (ev) {
      $("#match-movies").html("<i class='fa fa-spinner fa-spin'></i> movies that match your criteria.");
      var values = $("#slider-runtime").val().split(",");
      values.forEach(function (e, i) {
        if (!e)
          values = ["0", "200+"];

      });
      if (values[0].length < 1) values[0] = "0";
      var tip = $(this).parent().find(".tooltip-inner");
      tip.html(values[0] + " min - " + values[1] + " min");
      $("#runtime-min").val(values[0] + " min");
      $("#runtime-max").val(values[1] + " min");
      $("#runtime-max").val( (values[1] == 200) ? '200 min+' : values[1] + ' min' );
      var orderby = $( "input[name='orderby']" ).val();
      var language = $( "input[name='language']" ).val();
      var genre = $( "input[name='genre']" ).val();
      var mpaa = $( "input[name='mpaa']" ).val();
      showNumMovies(totalMovies, $( "#year-min" ).val().replace('< ', ''), $( "#year-max" ).val(), values[0], values[1], language, genre, mpaa, orderby);
    });

    $("#slider-year").slider()
    .on('slideStop', function () {
      $("#match-movies").html("<i class='fa fa-spinner fa-spin'></i> movies that match your criteria.");
      var values = $("#slider-year").val().split(",");
      values.forEach(function (e, i) {
        if (e == undefined)
          values[i] = "<?php echo $ymin?>";
      });
      if (values[0].length < 1) values[0] = "<?php echo $ymin?>";
      $("#year-min").val( (values[0] == 1950) ? '< 1950' : values[0] );
      $("#year-max").val(values[1]);
      var orderby = $( "input[name='orderby']" ).val();
      var language = $( "input[name='language']" ).val();
      var genre = $( "input[name='genre']" ).val();
      var mpaa = $( "input[name='mpaa']" ).val();
      showNumMovies(totalMovies, values[0], values[1], $( "#runtime-min" ).val().replace(' min',''), $( "#runtime-max" ).val().replace(' min','').replace('+', ''), language, genre, mpaa, orderby);
    });

    $("#language-filter a").click( function () {
      $("#match-movies").html("<i class='fa fa-spinner fa-spin'></i> movies that match your criteria.");
      var orderby = $( "input[name='orderby']" ).val();
      var language = $(this).html();
      var genre = $( "input[name='genre']" ).val();
      var mpaa = $( "input[name='mpaa']" ).val();
      var button = $(this).parent().parent().parent().find("button");
      button.html(language + " <span class='caret'></span>");
      $("#language-filter").find("input").val(language);
      showNumMovies(totalMovies, $( "#year-min" ).val().replace('< ', ''), $( "#year-max" ).val(), $( "#runtime-min" ).val().replace(' min',''), $( "#runtime-max" ).val().replace(' min','').replace('+', ''), language, genre, mpaa, orderby);
    });

    $("#genre-filter a").click( function () {
      $("#match-movies").html("<i class='fa fa-spinner fa-spin'></i> movies that match your criteria.");
      var orderby = $( "input[name='orderby']" ).val();
      var genre = $(this).html();
      var language = $( "input[name='language']" ).val();
      var mpaa = $( "input[name='mpaa']" ).val();
      var button = $(this).parent().parent().parent().find("button");
      button.html(genre + " <span class='caret'></span>");
      $("#genre-filter").find("input").val(genre);
      showNumMovies(totalMovies, $( "#year-min" ).val().replace('< ', ''), $( "#year-max" ).val(), $( "#runtime-min" ).val().replace(' min',''), $( "#runtime-max" ).val().replace(' min','').replace('+', ''), language, genre, mpaa, orderby);
    
    });
    $("#mpaa-filter a").click( function () {
      $("#match-movies").html("<i class='fa fa-spinner fa-spin'></i> movies that match your criteria.");
      var orderby = $( "input[name='orderby']" ).val();
      var mpaa = $(this).html();
      var genre = $( "input[name='genre']" ).val();
      var language = $( "input[name='language']" ).val();
      var button = $(this).parent().parent().parent().find("button");
      button.html(mpaa + " <span class='caret'></span>");
      $("#mpaa-filter").find("input").val(mpaa);
      showNumMovies(totalMovies, $( "#year-min" ).val().replace('< ', ''), $( "#year-max" ).val(), $( "#runtime-min" ).val().replace(' min',''), $( "#runtime-max" ).val().replace(' min','').replace('+', ''), language, genre, mpaa, orderby);
    });
    $("#orderby-filter a").click( function () {
      $("#match-movies").html("<i class='fa fa-spinner fa-spin'></i> movies that match your criteria.");
      var that = $(this);
      var orderby = that.html();
      var mpaa = $( "input[name='mpaa']" ).val();
      var genre = $( "input[name='genre']" ).val();
      var language = $( "input[name='language']" ).val();
      var button = that.parent().parent().parent().find("button");
      button.html(orderby + " <span class='caret'></span>");
      $("#orderby-filter").find("input").val(orderby);
      if (orderby.indexOf("IMDb") > -1)
        orderby = "imdb_rating";
      showNumMovies(totalMovies, $( "#year-min" ).val().replace('< ', ''), $( "#year-max" ).val(), $( "#runtime-min" ).val().replace(' min',''), $( "#runtime-max" ).val().replace(' min','').replace('+', ''), language, genre, mpaa, orderby, orderby);
    });

    $( "#year-min" ).val( $( "#slider-year" ).slider( "values", 0 ));
    $( "#year-max" ).val( $( "#slider-year" ).slider( "values", 1 ));
    $( "#runtime-min" ).val( $( "#slider-runtime" ).slider( "values", 0 ) + " min");
    $( "#runtime-max" ).val( $( "#slider-runtime" ).slider( "values", 1 ) + " min");
  
</script>
</body>
</html>