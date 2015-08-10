<?php

include 'includes/functions.php';

$id = $_GET['id'];

include 'includes/mysqli_connect.php';

include 'includes/login_check.php';

include 'includes/track_page_view.php';

$memcached = new Memcached();
$memcached->addServer('localhost', 11211) or die ("Could not connect");

//$movie = $memcached->get("movie_$id");
$movie = false;

if (!$movie) {
  $movie = array();

  $query1 = "SELECT * FROM movies INNER JOIN imdb ON movies.id=imdb.id WHERE movies.id=$id";
  if($t = $mysqli->query($query1)) {
    if ($result = mysqli_fetch_assoc($t)) {
      $movie['params'] = $result;
    }
  }

  $movie['actors'] = array();
  $starringQuery = "SELECT roles.actor_id, roles.character, actors.name, actors.photo
                    FROM roles
                    INNER JOIN actors
                    ON roles.actor_id = actors.id
                    AND roles.movie_id = $id
                    ORDER BY roles.star DESC";
  if($s = $mysqli->query($starringQuery)) {
    while ($actor = mysqli_fetch_assoc($s)) {
      $movie['actors'][] = $actor;
    }
  }

  if ($movie['params']['director']) {
    if ($d = $mysqli->query("SELECT photo, name FROM actors WHERE id={$movie['params']['director']}")) {
      if ($director = mysqli_fetch_assoc($d))
        $movie['director'] = $director;
    }
  }

  $movie['keywords'] = array();
  if ($kq = $mysqli->query("SELECT k.keyword, k.keyword_id FROM keywords k INNER JOIN movie_keywords m ON k.keyword_id = m.keyword_id WHERE m.id=$id")) {
    while ($kw = mysqli_fetch_assoc($kq)) {
      $movie['keywords'][] = $kw;
    }
  }

  $memcached->set("movie_$id", $movie);

}


// $query1 = "SELECT * FROM movies WHERE id=$id";
// if($t = $mysqli->query($query1)) {
//   if ($result = mysqli_fetch_assoc($t)) {
//     $params = $result;
//   }
// }

// $actors = array();
// $starringQuery = "SELECT roles.actor_id, roles.character, actors.name, actors.photo
//                   FROM roles
//                   INNER JOIN actors
//                   ON roles.actor_id = actors.id
//                   AND roles.movie_id = $id
//                   ORDER BY roles.star DESC";
// if($s = $mysqli->query($starringQuery)) {
//   while ($actor = mysqli_fetch_assoc($s)) {
//     $actors[] = $actor;
//   }
// }

$noActors = (sizeof($movie['actors'])) ? false : true;
$actors = $movie['actors'];
//$photos[] = ((strlen($actor['photo']) > 0) ? 'http://image.tmdb.org/t/p/w185' . $actor['photo'] : "images/no_actor_found.png");

$noResult = false;
$params = $movie['params'];
//echo (sizeof($params)) ? "yes" : "no";

if (!$params)
  $noResult = true;

gen_page_header((($noResult) ? "Not valid" : "{$params['title']} ({$params['year']})") . " | readyto.watch");

?>

<body>

<?php
include 'includes/navbar.php';

include 'includes/left_navbar.php';
?>
<div id="container">
<?php

if (!$noResult)
  include 'templates/gen_movie_page.php';
else
  echo "<div class='message-danger'><i class='fa fa-exclamation-triangle'></i> This is not a valid movie! <a class='return-to-search'>Try another search</a>.</div>";

// close the connection
$mysqli->close();

?>
</div> <!-- #container -->

<?php

$addedScript = "<script>
$( document ).ready( function () {
  $( window ).scrollTop($('.img-box.lg').offset().top - 80);
});

// read-more animation
var button = $('.about:visible .read-more-button'),
    p = button.parent(),
    lg = $('.movie-lg:visible'),
    offset = lg.offset().top,
    abt = $('.about:visible'),
    total = abt.height(),
    trailerHeight = 0,
    star = $('.starring:visible'),
    mobile = $('.mobile-mov-info.reveal'),
    trailer = $('.trailer');
if (trailer.length)
  trailerHeight = trailer.offset().top + trailer.height() - offset + 5;

var maximum = Math.max(200, star.offset().top + star.height() - offset-5, trailerHeight, mobile.offset().top + mobile.height() - offset);
var abtmax = maximum - abt.position().top;
abt.css('max-height', abtmax);

if (total <= abtmax) {
  p.hide();
  abt.css('max-height', 9999);
}

button.click(function() {
  // fade out read-more
  p.hide();
  abt.css({'max-height' : 9999}); 
  // prevent jump-down
  return false;
});

$('#nav-search').val('" . addslashes($params['title']) . "');
$('#nav-search').css('font-weight', 'bold');
addRelated($('.related-container'), true, 6);

var yt_int, yt_players={}, 
initYT = function () {
  $('.ytplayer').each(function() {
    yt_players[this.id] = new YT.Player(this.id);
  });
};
  
$.getScript('//www.youtube.com/player_api', function() {
  yt_int = setInterval(function(){
    if(typeof YT === 'object'){
      initYT();
      clearInterval(yt_int);
    }
  },500);
});

$('#trailerModal').on('shown.bs.modal', function () {
  yt_players['trailer'].playVideo();
});

$('#trailerModal').on('hide.bs.modal', function () {
  yt_players['trailer'].pauseVideo();
});

var count, interval, div = $('.cast-container');
var left = $('.cast-scroll-left'), right = $('.cast-scroll-right');
div.hover( function () {
  right.show();
  if (div.scrollLeft() > 0) {
    left.show();
  }
}, function () {
  $('.cast-scroll-right, .cast-scroll-left').fadeOut();
});

right.on('mouseover', function() {
  interval = setInterval( function () {
    count = count || 2;
    pos = div.scrollLeft();
    div.scrollLeft(pos + count);
  }, 5);
}).on('mouseout', function () {
  clearInterval(interval);
});

left.on('mouseover', function() {
  interval = setInterval( function () {
    count = count || 2;
    pos = div.scrollLeft();
    div.scrollLeft(pos - count);
  }, 5);
}).on('mouseout', function () {
  clearInterval(interval);
});

div.scroll( function () {
  var thisDiv = $(this);
  left = thisDiv.find('.cast-scroll-left');
  if (div.scrollLeft() == 0) {
    left.fadeOut();
  } else {
    left.show();
  }
});

</script>";
if ($admin)
  $addedScript .= "<script src='js/admin.js'></script>";

gen_page_footer($addedScript);

?>