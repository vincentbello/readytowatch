<?php

define("COOKIE_NAME", "session");
define("COOKIE_DURATION", 60); // seconds

class Session {
	var $token;
	var $version;
	var $username;
	var $expiration;

	function __construct($token, $version, $username, $expiration) {
		$this->token = $token;
		$this->version = $version;
		$this->username = $username;
		$this->expiration = $expiration;
	}

	function incr() {
		return new Session($token, $version + 1, $username, $expiration);
	}

	function expiration() {
	 	return time() + COOKIE_DURATION;
	}

	function createToken() {
		return substr(md5(rand()), 0, 20);
	}

	function createSession($usr) {
		return new Session(createToken(), 0, $usr, expiration());
	}

	function cookieExport() {
		setcookie(COOKIE_NAME, $token, $expiration);
	}
}

function getSessionFromCookie() {}

?>