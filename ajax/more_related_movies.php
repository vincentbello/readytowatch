<?php
include_once '../includes/mysqli_connect.php';
include_once '../includes/functions.php';
$id = $_POST['id'];
$offset = $_POST['offset'];
$more = $_POST['more'];

$relatedMovies = array();
$query = "SELECT rltd.related_id, mvs.title, mvs.year, mvs.img_link
          FROM related rltd
          JOIN movies mvs
          ON rltd.related_id = mvs.id
          AND rltd.id = $id
          LIMIT $more OFFSET $offset";

if ($r = $mysqli->query($query)) {
  while ($relatedMov = mysqli_fetch_assoc($r)) {
    $relatedMovies[] = $relatedMov;
  }
}

if (count($relatedMovies) == 0) {
  $query = "SELECT mvs.id, m.title, m.year, m.img_link
      FROM movie_keywords mvs
      JOIN movie_keywords mvs2
      ON mvs2.id = $id 
      AND mvs.keyword_id = mvs2.keyword_id
      JOIN movies m
      ON (m.id = mvs.id AND mvs.id != $id)
      GROUP BY mvs.id
      HAVING COUNT(*) >= 3
      ORDER BY COUNT(*) DESC
      LIMIT $more OFFSET $offset";
  
  if($relatedMovs = $mysqli->query($query)) {
    while($relatedMov = mysqli_fetch_assoc($relatedMovs)) {
      mysqli_query($mysqli, "INSERT INTO related VALUES ($id, {$relatedMov['id']})");
      $relatedMovies[] = $relatedMov;
    }
  }
}


$moreRelated = false;
if (count($relatedMovies) == $more)
  $moreRelated = true;

if (count($relatedMovies) > 0) {
 	foreach($relatedMovies as $mov) {
 		$movUrl = $mov['related_id'] . "/" . rewriteUrl($mov['title']);
 		echo "<div class='related-mov'>
 			<a href='movie/$movUrl'><img alt='{$mov['title']}' src='" . ((strlen($mov['img_link'])>0) ? "http://image.tmdb.org/t/p/w185{$mov['img_link']}" : "images/no_image_found.png") . "'></a>
 			<div class='mov-caption'>
 				<a href='movie/$movUrl'>{$mov['title']}</a> <span class='year'>{$mov['year']}</span>
 			</div>
 		</div>";
 	}
	if ($moreRelated) {
			
		echo "<div class='more-related'>
			<div class='get-more-related' data-toggle='tooltip' data-placement='top' title='Show more'><span>...</span></div>
		</div>";
		
	}
	echo "</div></div>";

} else {
  echo "<div class='none'><i class='fa fa-exclamation-triangle'></i> We couldn't find any movies related to this.</div>";
}
?>