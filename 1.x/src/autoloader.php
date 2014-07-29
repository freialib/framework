<?php namespace app\main;

/**
 * @return \hlin\archetype\Autoloader or null on failure
 */
function autoloader($srcpath) {

	$composerjson = "$srcpath/composer.json";

	if ( ! file_exists($composerjson)) {
		return null;
	}

	$env = json_decode(file_get_contents($composerjson), true);
	$paths = $env['autoload']['freia'];

	// include composer autoloader
	require "$srcpath/vendor/autoload.php";

	// initialize
	$autoloader = \freia\SymbolLoader::instance($srcpath, $paths);

	// add as main autoloader
	if ( ! $autoloader->register(true)) {
		return null;
	}

	// fulfill archetype contract before returning
	return \freia\Autoloader::wrap($autoloader);
}
