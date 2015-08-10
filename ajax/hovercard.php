<?php

require_once '../includes/mysqli_connect.php';
require_once '../includes/functions.php';

$type = $_GET['type'] ?: 'a';
$id = $_GET['id'];

if ($type == 'a') {
	if($q = $mysqli->query("SELECT a.id,a.name,a.photo,a.dob,a.dod,a.backdrop,a.backdrop_id,p.label FROM actors a INNER JOIN person_type p ON a.person_id = p.person_id WHERE a.id=$id")) {
		while($actor = mysqli_fetch_assoc($q)) {

			// get movies he/she has starred in.

			$rolesQuery = "SELECT movies.id, movies.title, movies.year
					  		FROM movies
					  		INNER JOIN roles
					  		ON roles.movie_id = movies.id
					  		WHERE roles.actor_id = $id
					  		ORDER BY movies.popularity DESC, roles.star DESC
					  		LIMIT 3";

			$knownFor = array();

			if($roles = $mysqli->query($rolesQuery)) {
				while($role = mysqli_fetch_assoc($roles)) {
					$knownFor[] = "<a href='movie/{$role['id']}/". rewriteUrl($role['title']) ."'>{$role['title']}</a> ({$role['year']})";
				}
			}

			$kf = implode(', ', $knownFor);

			if ($bdq = $mysqli->query("SELECT title,year FROM movies WHERE id = {$actor['backdrop_id']}")) {
				$bd = mysqli_fetch_assoc($bdq);
			}

			echo "<div class='hovercard' data-hovid='$id'><div class='hovercard-wrapper'>";

			$backdrop = ($actor['backdrop']) ? ("http://image.tmdb.org/t/p/w300" . $actor['backdrop']) : "images/no_backdrop.png";


			echo "<div class='hovercard-contents'>";
			echo "<div class='hov-triangle'></div>";
			echo "<div class='hov-backdrop'>
					<div class='hov-backdrop-wrapper'>
					</div>
					<img src='$backdrop' alt='backdrop' class='hov-backdrop-img'>";

			// echo "<div class='triangle-image'>
   //      			<div class='deg-fix'>
   //          			<img class='image-fix' src='$backdrop'/>
   //      			</div>
   //  			</div>";

			if ($bd) {
				echo "<div class='backdrop-title'>
				<b><a href='movie/{$actor['backdrop_id']}/". rewriteUrl($bd['title']) ."'>{$bd['title']}</a></b> ({$bd['year']})
				</div>";
			}
			echo "</div>";

			echo "<div class='hovercard-text'>";
			echo "<div class='hov-photo'><a href='actor/{$actor['id']}/" . rewriteUrl($actor['name']) . "'><img src='" . (($actor['photo']) ? "http://image.tmdb.org/t/p/w92{$actor['photo']}" : "images/no_actor_found.png") . "' width=80></a></div>";
			echo "<div class='hov-header'>";
			echo "<a class='hov-title' href='actor/{$actor['id']}/" . rewriteUrl($actor['name']) . "'>{$actor['name']}</a>";
			if ($actor['label'] && $actor['label'] !== 'N/A')
				echo "<div class='age'><i class='fa fa-film'></i> " . str_replace("|", " &#183; ", $actor['label']) . "</div>";
			echo "<div class='age'>" . birth_and_death($actor['dob'], $actor['dod']) . "</div>";
			echo "</div>";

			if ($kf) echo "<div class='kf'><b>Known for</b>: $kf.</div>";
			echo "</div>";
			echo "</div></div></div>";

		}
	}
}

?>