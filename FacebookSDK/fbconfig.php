<?php
session_start();

require_once __DIR__ . '/autoload.php';
require_once __DIR__ . '/../includes/functions.php';

$userIdRedirect = (isset($_GET['userid'])) ? "?userid={$_GET['userid']}" : "";
$userId = (isset($_GET['userid'])) ? $_GET['userid'] : NULL;

use Facebook\FacebookSession;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequest;
use Facebook\FacebookResponse;
use Facebook\FacebookSDKException;
use Facebook\FacebookRequestException;
use Facebook\FacebookAuthorizationException;
use Facebook\GraphObject;
use Facebook\Entities\AccessToken;
use Facebook\HttpClients\FacebookCurlHttpClient;
use Facebook\HttpClients\FacebookHttpable;

// init app with app id and secret
FacebookSession::setDefaultApplication( '251415491724512','b3ac72ac8a84b5b77dc3d2cd636b22e5' );

//login helper with redirect URI
$helper = new FacebookRedirectLoginHelper("http://readyto.watch/FacebookSDK/fbconfig.php$userIdRedirect");

try {
	$session = $helper->getSessionFromRedirect();
} catch (FacebookRequestException $e) {
	// when Facebook returns an error
	echo "Facebook fails<br>";
	echo $e->getHttpStatusCode();
	echo $e->getErrorType();
	echo $e->getMessage();
} catch (Exception $e) {
	// validation fails, or other local issues
	echo "validation fails";
}

// see if we have a session
if ( isset($session) ) {
	//echo "now we're here";
	// graph API request for user data
	$request = new FacebookRequest( $session, 'GET', '/me');
	$response = $request->execute();

	// get response
	$graphObject = $response->getGraphObject();
	$fbId = $graphObject->getProperty('id');
	$fbFirstName = $graphObject->getProperty('first_name');
	$fbLastName = $graphObject->getProperty('last_name');
	$fbEmail = $graphObject->getProperty('email');

	$respCode = checkFbUser($fbId, $fbFirstName, $fbLastName, $fbEmail, $userId);
	if ($respCode == 1) {
		header("Location: ../account/$respCode");
	} else {
		// Session variables
		$_SESSION['fbId'] = $fbId;
		
		if ($respCode == 2) {
			header("Location: ../account");
		} else if ($respCode == 0) {
			header("Location: ../");
		}
	}
} else {
	//echo "there";
	$loginUrl = $helper->getLoginUrl();
	//echo $loginUrl;
	header("Location: $loginUrl");
}

?>