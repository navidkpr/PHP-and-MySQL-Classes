<?php
	session_start();

	$GLOBALS['config'] = array(
		'mysql' => array(
			'host' => '127.0.0.1', // Feel with your own ip, username, password and db to use
			'username' => 'username',
			'password' => 'pasword',
			'db' => 'db'
		),
		'remember' => array(
			'cookie_name' => 'hash',
			'cookie_expiry' => 604800 //one month in seconds
		),
		'session' => array(
			'session_name' => 'nope',
			'token_name' => 'token',
			'room_name' => 'roomKey'
		),
		'register_fields_requierments' => array(
			'username' => array(
				'required' => true,
				'min' => 2,
				'max' => 20,
				'unique' => 'users'
			),
			'password' => array(
				'required' => true,
				'max' => 20,
				'min' => 6
			),
			'repassword' => array(
				'required' => true,
				'min' => 2,
				'max' => 20,
				'match' => 'password'
			),
			'name' => array(
				'required' => true,
				'min' => 2,
				'max' => 50,
			),
			'email' => array(
				'required' => true,
				'min' => 3,
				'max' => 100,
				'unique' => 'email'
			)
		)
	);

	function spl_func($class) {
		require_once 'classes/' . $class . '.php';
	}

	spl_autoload_register(function($class) {
		require_once 'classes/' . $class . '.php';
	});

	require_once 'functions/sanitize.php';
?>
