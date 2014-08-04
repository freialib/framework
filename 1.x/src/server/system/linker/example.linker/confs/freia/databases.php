<?php return [

	'example.mysql' => [
		'dsn' => 'mysql:host=localhost;dbname=freia-example;charset=utf8',
		'username' => 'root',
		'password' => 'root',
		'options' => [
			\PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
		],
		'attributes' => [
			\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
			\PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
		],
		'timezone' => date_default_timezone_get(),
		'pdx:lock' => false,
	]

]; # conf
