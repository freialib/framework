<?php return array
	(
		'example.mysql' => array
			(
				'dsn' => 'mysql:host=localhost;dbname=freia-example;charset=utf8',
				'username' => 'root',
				'password' => 'root',
				'options' => array
					(
						\PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
					),
				'attributes' => array
					(
						\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
						\PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
					),
				'timezone' => date_default_timezone_get(),
				'pdx:lock' => false,
			)

	); # conf
