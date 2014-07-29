<?php namespace app\main;

/**
 * ...
 */
function main($wwwconf) {

	// php settings
	date_default_timezone_set('Europe/London');

	$srcpath = realpath(__DIR__.'/..');
	$syspath = realpath(__DIR__);
	$logspath = realpath("$syspath/app/logs");

	// fatalerror logging
	require "$syspath/app/fatalerrors.php";
	fatalerrors($logspath);

	// init autoloader
	require "$srcpath/autoloader.php";
	$autoloader = autoloader($srcpath);

	// path to sources
	$srcpath = realpath(__DIR__.'/..');

	// load in theme
	require "$srcpath/theme/main.php";

	if ($autoloader === null) {
		error_log("Failed loading autoloader.");
		return 500;
	}

	// init main context
	require "$syspath/app/context.php";
	$context = context($srcpath, $syspath, $logspath, $autoloader);

	// example logic
	try {

		$dbconf = $context->confs->read('freia/databases');
		$mysql = \fenrir\MysqlDatabase::instance($dbconf['demo.mysql']);
		$http = \fenrir\HttpDispatcher::instance($context);

		require "$syspath/protocols/main.php";
		$auth = authorizer($context, $mysql);

		if ( ! $auth->can('access:site')) {
			return 401;
		}

		require "$syspath/routes/main.php"; # => router function
		$exitcode = router($syspath, $http, $context, $mysql);

		return $exitcode;
	}
	catch (\Exception $exception) {
		$context->logger->logexception($exception);
		return 500;
	}
}
