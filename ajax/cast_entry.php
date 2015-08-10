<?php
require_once '../includes/mysqli_connect.php';
require_once '../includes/functions.php';

$id = $_POST['id'];

$relatedMovies = array();
$query = "SELECT a.id, a.name, a.photo, r.character FROM roles r INNER JOIN actors a ON a.id = r.actor_id AND r.movie_id = $id ORDER BY r.star DESC";
$response = "";

if ($r = $mysqli->query($query)) {
  if ($director = mysqli_fetch_assoc($mysqli->query("SELECT a.photo, a.name, a.id FROM actors a INNER JOIN movies m on m.director = a.id WHERE m.id = $id"))) {
    $response .= "
    <div class='complete-cast-director'>
      <h4>Director</h4>
      <img src='" . ((strlen($director['photo']) > 0) ? "http://image.tmdb.org/t/p/w92{$director['photo']}" : "images/no_image_found.png") . "'>
      <div class='cast-entry-names' style='margin-left:60px'>
        <a class='actorLink' data-hovercard='{$director['id']}' href='actor/{$director['id']}/" . rewriteUrl($director['name']) . "'>
          <b>" . $director['name'] . "</b>
        </a>
      </div>
    </div>";
  }

  $response .= "<h4>Cast</h4>";
  while ($actor = mysqli_fetch_assoc($r)) {
    $response .= "
    <div class='complete-cast-entry'>
      <img src='" . ((strlen($actor['photo']) > 0) ? "http://image.tmdb.org/t/p/w92{$actor['photo']}" : "images/no_image_found.png") . "'>
      <div class='cast-entry-names'>
        <a class='actorLink' data-hovercard='{$actor['id']}' href='actor/{$actor['id']}/" . rewriteUrl($actor['name']) . "'>
          <b>" . $actor['name'] . "</b>
        </a><br><span class='char-indent'>" . stripslashes($actor['character']) . "</span>
      </div>
    </div>";
  }
}

echo ((strlen($response) > 0) ? $response : "Sorry, we don't have the cast for this movie.");
?>