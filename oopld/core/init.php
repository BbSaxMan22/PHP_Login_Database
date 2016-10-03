<?php

// include on each page
// define things like start session, set config
// autoload classes
// include functions used, e q sanitize

session_start();

$GLOBALS['config'] = array(
	'mysql' => array(
		'host' => 'localhost',
		'username' => 'root',
		'password' => '',
		'db' => 'user_login_prac'
	),
	'remember' => array(
		'cookie_name' => 'hash',
		'cookie_expiry' => 604800
	),
	'session' => array(
		'session_name' => 'user',
		'token_name' => 'token'
	)
);

// autoload in classes when they are required
// require_once 'classes/Config.php'
// require_once 'classes/Cookie.php'
//the following instead of doing this over and over
// great if filed move

spl_autoload_register(function($class) {
	require_once 'classes/' . $class . '.php';
});//only requiring things as we need them

require_once 'functions/sanitize.php';

if (Cookie::exists(Config::get('remember/cookie_name')) && !Session::exists(Config::get('session/session_name'))) {
	$hash = Cookie::get(Config::get('remember/cookie_name'));
	$hashCheck = DB::getInstance()->get('users_session', array('hash', '=', $hash));

	if ($hashCheck->count()) {
		// hash matches log the user in
		$user = new User($hashCheck->first()->user_id);
		$user->login();
	}
}