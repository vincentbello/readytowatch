<?php // includes/login_check.php
session_start();

$timestamp = date('Y-m-d H:i:s');

$loggedIn = isLoggedIn($_SESSION);

if (isset($_POST[ 'login_username' ]) && isset( $_POST[ 'login_password' ])) {
	$_POST['login_username'] = $_POST['full_login_username'];
	$_POST['login_password'] = $_POST['full_login_password'];
}

if (isset($_POST[ 'login_username' ]) && isset( $_POST[ 'login_password' ])) {
	// Clean up the post data
	$username = strip_tags( $_POST['login_username'] );
	//hash the entered password for comparison with the db
	$password = hash("sha256", $_POST['login_password']);
	//Check for a record that matches the POSTed credentials
	$query = "SELECT * FROM users WHERE username = '$username' AND password = '$password';";
	$result = $mysqli->query($query);
	$user = mysqli_fetch_assoc($result);
	if ( $result && $result->num_rows == 1 && $user['active'] == 1 ) {
		$_SESSION['user'] = $_POST['login_username'];
		if (isset($_POST['stay-logged']) || isset($_POST['stay-logged-full'])) {
			onLogin($result['id']);
		}
	}
	mysqli_query($mysqli, "INSERT INTO session_history VALUES ({$result['id']}, 'login', '$timestamp')");
}

$remembered = rememberMe();
if ($remembered) {
	$account = mysqli_fetch_assoc($mysqli->query("SELECT * FROM users WHERE id=$remembered"));
	$_SESSION['user'] = $account['username'];
	$_SESSION['fbId'] = $account['fb_id'];
}

if ($loggedIn) {
	$fb = isset($_SESSION['fbId']);
	$user = isset($_SESSION['user']);
	$dbAccountFinder = $fb ? "fb_id='{$_SESSION['fbId']}'" : "username='{$_SESSION['user']}'";
	$accountQuery = "SELECT * FROM users WHERE $dbAccountFinder";
	if (!$account)
		$account = mysqli_fetch_assoc($mysqli->query($accountQuery));
}

$admin = ($loggedIn && ($account['id'] == 21)) ? true : false;

?>