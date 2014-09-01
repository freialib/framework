<?php namespace app\main;

/**
 * ...
 */
function main($domain) {

	// php settings
	date_default_timezone_set('Europe/London');

	// current path
	$rootpath = realpath(__DIR__);

	// we let the domain decide the logspath and cachepath
	$logspath = $domain['logspath'];
	$cachepath = $domain['cachepath'];

	// context path is in our case the server source root
	// we use it to link different server programs
	$contextpath = realpath(__DIR__.'/..');

	// fatalerror logging
	require "$rootpath/linker/fatalerrors.php";
	fatalerrors($logspath);

	// init autoloader
	require "$contextpath/autoloader.php";
	$autoloader = autoloader($domain['prjpath'], $domain['environment'] == 'development' ? true : false);
	if ($autoloader === null) {
		error_log("[Critical-Error] Failed loading autoloader.");
		return 500;
	}

	// package information
	$pkg = json_decode(file_get_contents("{$domain['prjpath']}/composer.json"), true);

	// init main context
	require "$rootpath/linker/context.php";
	$context = context($autoloader, $pkg, $rootpath, $logspath, $cachepath);

	// load in theme
	require "$contextpath/theme/main.php";

	// example logic
	try {

		$dbconf = $context->confs->read('freia/databases');
		$mysql = \fenrir\MysqlDatabase::instance($dbconf['example.mysql']);
		$http = \fenrir\HttpDispatcher::instance($context);

		require "$rootpath/protocols/main.php";
		$auth = authorizer($context, $mysql);

		if ( ! $auth->can('access:site')) {
			return 401;
		}

		require "$rootpath/routes/main.php"; # => router function
		$exitcode = router($domain, $rootpath, $http, $context, $mysql);

		return $exitcode;
	}
	catch (\Exception $exception) {
		$context->logger->logexception($exception);
		return 500;
	}
}
