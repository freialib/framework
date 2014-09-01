<?php namespace app\main;

/**
 * @return \hlin\archetype\Autoloader or null on failure
 */
function autoloader($prjpath, $debugMode = false) {

	$composerjson = "$prjpath/composer.json";

	if ( ! file_exists($composerjson)) {
		return null;
	}

	$pkg = json_decode(file_get_contents($composerjson), true);
	$env = $pkg['extra']['freia'];

	// include composer autoloader
	require "$prjpath/{$pkg['config']['vendor-dir']}/autoload.php";

	// initialize
	$autoloader = \freia\autoloader\SymbolLoader::instance($prjpath, $env, $debugMode);

	// add as main autoloader
	if ( ! $autoloader->register(true)) {
		return null;
	}

	// fulfill archetype contract before returning
	return \freia\Autoloader::wrap($autoloader);
}
