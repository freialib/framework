<?php namespace app\main;

	$wwwpath = realpath(__DIR__);
	$siteroot = realpath("$wwwpath/..");

	// ignore existing files in PHP build-in server
	$uri = $_SERVER['REQUEST_URI'];
	if (strpos($uri, '..') == false) {
		$req = "$wwwpath/$uri";
		if (file_exists($req) && is_file($req)) {
			return false;
		}
	}

	// private keys, server settings and sensitive information
	$domainconf = include "$siteroot/domain.conf.php";
	$domainconf['wwwpath'] = $wwwpath;

	require "{$domainconf['systempath']}/main.php";

	// invoke main
	$exitcode = main($domainconf);

	// handle system failure
	if ($exitcode != 0) {
		$errpage = "$wwwpath/err/$exitcode.html";
		if (file_exists($errpage)) {
			http_response_code($exitcode);
			include $errpage;
		}
		else { // no error page for exitcode
			$errpage = "$wwwpath/err/500.html";
			http_response_code($exitcode);
			include $errpage;
		}
	}
