<?php

function getData($endpoint) {
    $session = curl_init($endpoint);
    curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
    $data = curl_exec($session);
    curl_close($session);
    return json_decode($data);
}

function gen_time($date) {
    date_default_timezone_set("America/New_York");
    $timenow = time();
    $timebefore = strtotime($date);
    $sb = $timenow - $timebefore;
    $response = "As of ";
    if ($sb < 10) {
        $response .= "just now";
    } else if ($sb < 60) {
        $response .= "$sb second" . (($sb > 1)?"s":"") . " ago";
    } else if ($sb < 3600) {
        $response .= floor($sb/60) . " minute" . ((floor($sb/60) > 1)?"s":"") . " ago";
    } else if ($sb < 86400) {
        $response .= floor($sb/3600) . " hour" . ((floor($sb/3600) > 1)?"s":"") . " ago";
    } else if ($sb < 129600) {
        $response .= "yesterday";
    } else {
        $response .= floor($sb/(60*60*24)) . " days ago";
    }
    return $response;
}

// get itunes link/price
function get_itunes_link($title, $runtime, $director, $year = 0) {

	$t = urlencode($title);
	$endpoint = "https://itunes.apple.com/search?term=$t&media=movie&entity=movie&attribute=movieTerm";
	$r = getData($endpoint);
	if ($r === NULL) die('No links found.');
	$link = ["link"=>"","rent"=>"", "buy"=>"", "itunesId"=>""];
	$directorMatch = true;
	if ($r->resultCount > 0) {
		for ($j = 0; $j < $r->resultCount; $j++) {
            $result = $r->results[$j];
			if ((abs(floor(($result->trackTimeMillis)/60000) - $runtime) < 20) &&
				((levenshtein($result->trackName, $title) < 5) || (strpos($result->trackName, $title) !== false))) {
				if ( (strpos($result->artistName, $director) !== false)
                    || ( !$director && ($result->trackName == $title) && (substr($result->releaseDate,0,4) == $year) ) ) {	
                    $link['link'] = $result->trackViewUrl;
					$rentArr = array();
					$buyArr = array();
					$rentArr[] = $result->trackRentalPrice;
					$rentArr[] = $result->trackHdRentalPrice;
					$buyArr[] = $result->trackPrice;
					$buyArr[] = $result->trackHdPrice;
					$link['rent'] = implode("|", $rentArr);
					$link['buy'] = implode("|", $buyArr);
					$link['itunesId'] = $result->trackId;
					break;
				}
			}
		}
	}
	return $link;
}

function get_itunes_link_id($itunesId) {

    $endpoint = "https://itunes.apple.com/lookup?id=$itunesId";
    $r = getData($endpoint);
    if ($r === NULL) die('No links found.');
    $link = ["link"=>"","rent"=>"", "buy"=>"", "itunesId"=>$itunesId];
    if ($r->resultCount > 0) {
        $link['link'] = $r->results[0]->trackViewUrl;
        $rentArr = array();
        $buyArr = array();
        $rentArr[] = $r->results[0]->trackRentalPrice;
        $rentArr[] = $r->results[0]->trackHdRentalPrice;
        $buyArr[] = $r->results[0]->trackPrice;
        $buyArr[] = $r->results[0]->trackHdPrice;
        $link['rent'] = implode("|", $rentArr);
        $link['buy'] = implode("|", $buyArr);
    }
    return $link;
    
}

function get_netflix_link($title, $year) {

    $years_from_search = array();
    $links_from_search = array();
    $titles_from_search = array();
    $html = file_get_contents("http://instantwatcher.com/titles?q=" . urlencode($title));
    $matches = strallpos($html, "<span class=\"releaseYear\">");
    $link_matches = strallpos($html, "nflx.openPlayer");
    $title_matches = strallpos($html, "class=\"title-list-item-link");
    $netflix = "";
    if (sizeof($matches) == sizeof($link_matches)) {

        for($i = 0; $i < sizeof($matches); $i++) {
            $years_from_search[] = substr($html, $matches[$i]+26, 4);
            $netflixId = preg_split('/[^0-9]/', substr(strstr(substr($html, $link_matches[$i]+30), "titles/movies/"), 14, 20))[0];
            $links_from_search[] = "http://www.netflix.com/WiMovie/$netflixId";
            $titles_from_search[] = substr(strstr(strstr(substr($html, $title_matches[$i]), ">"), "</a>", true), 1);
    
            if (($years_from_search[$i] == $year) && (levenshtein($titles_from_search[$i], $title) < 2)) {
                $netflix = $links_from_search[$i];
                break;
            }
        }
    }
    return $netflix;
}

function strallpos($pajar, $aguja, $offset=0, &$count=null) { 
  if ($offset > strlen($pajar)) trigger_error("strallpos(): Offset not contained in string.", E_USER_WARNING); 
  $match = array(); 
  for ($count=0; (($pos = strpos($pajar, $aguja, $offset)) !== false); $count++) { 
    $match[] = $pos; 
    $offset = $pos + strlen($aguja); 
  } 
  return $match; 
}

function get_youtube_link($title, $castArr) {
	$endpoint = "https://www.googleapis.com/youtube/v3/search?part=snippet&maxResults=10&q=".urlencode($title)."&type=video&videoCategoryId=30&key=AIzaSyBOaDIBNUCfbthPQ0XSZScnqI8jyxJ9G5Q";
	$session = curl_init($endpoint);
	curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
	$data = curl_exec($session);
	curl_close($session);
	$m = json_decode($data);
	if ($m === NULL) die('Error parsing json');
	$videoId = "";
	
	foreach($m->items as $movie) {
		$desc = $movie->snippet->description;
        if (levenshtein($title, $movie->snippet->title) < 2) {
		  foreach($castArr as $actor) {
		      if (strpos($desc, $actor) !== false) {
				return $movie->id->videoId;
			 }
		  }
        }
	}
	return "";
}


// get crackle link
function get_crackle_link($title) {
	$title = preg_replace('/[^a-z0-9]+/i', ' ', $title);
	if (strlen(file_get_contents("http://www.crackle.com/c/" . str_replace(' ', '-', $title))) > 0)
        return "http://www.crackle.com/c/" . str_replace(' ', '-', $title);
    else
    	return "";
}

// ASIN = B008PHN6F6

// get amazon link/prices
function get_amazon_link($title, $runtime, $cast) {
	require "amazon/amazon_api_class.php";
	$title = preg_replace('/[^a-z0-9]+/i', ' ', $title);
    $obj = new AmazonProductAPI();
    try {
        $result = $obj->searchProducts($title,
        	                              AmazonProductAPI::VIDEO,
                                          "TITLE");
    }
    catch (Exception $e) {
        echo $e->getMessage();
    }

    $link = ["link"=>"","rent"=>"", "buy"=>"", "asin"=>""];
    $test = true;
    
    foreach($result->Items->Item as $k) {
        if ($test) {
            echo $k;
            $test = false;
        }
    if ($k->ItemAttributes->Binding == 'Amazon Instant Video') {
    if (((abs($k->ItemAttributes->RunningTime - $runtime) < 5) && (strpos($cast,(string)$k->ItemAttributes->Actor) !== false))
    	|| ((abs($k->ItemAttributes->RunningTime - $runtime) < 5) && (levenshtein($title,(string)$k->ItemAttributes->Title) <= 3))) {
    	$link['link'] = $k->DetailPageURL;
    	$link['asin'] = $k->ASIN; 
    	break;
    } }	}
    if (strlen($link['link']) > 0) {
        $html = file_get_contents($link['link']);
            if ((!strpos($html, 'movie is currently unavailable')) && (strlen($html) > 20)) {
            //$pos = strpos($html, '<strong>Release year:</strong> ');
            //$release = substr($html, $pos+31, 4);
            //echo $release;
            $html = substr($html, strpos($html, '<ul class="button-list">'), 2000);
            $matches = array();
            $rent = "";
            $buy = "";
            preg_match_all("/Rent SD [$][1-9]+[.][1-9][1-9]/", $html, $matches);
            if (sizeof($matches[0]) > 0) $rent .= substr($matches[0][0], 8);
            preg_match_all("/Rent HD [$][1-9]+[.][1-9][1-9]/", $html, $matches);
            if (sizeof($matches[0]) > 0) $rent .= "|" . substr($matches[0][0], 8);
        	preg_match_all("/Buy SD [$][1-9]+[.][1-9][1-9]/", $html, $matches);
            if (sizeof($matches[0]) > 0) $buy .= substr($matches[0][0], 7);
            preg_match_all("/Buy HD [$][1-9]+[.][1-9][1-9]/", $html, $matches);
            if (sizeof($matches[0]) > 0) $buy .= "|" . substr($matches[0][0], 7);
        
        	$link['rent'] = $rent;
            $link['buy'] = $buy;
    	} else {
    	   $link['link'] = '';
    	}
    }
	return $link;
}

function verify_google_play($id) {
    $html = file_get_contents("https://play.google.com/store/movies/details?id=$id");
    if (strpos($html, "<title>Not Found</title>") !== false)
        return false;
    else
        return true;
}

?>