<?php namespace app\main;

use \hlin\archetype\Autoloader;

/**
 * @return \hlin\archetype\Context
 */
function context(Autoloader $autoloader, array $pkg, $rootpath, $logspath, $cachepath) {

	if ($logspath === false || empty($logspath)) {
		log_error('[ERROR] Bad logging path! No file logging will be available.');
	}

	// paths to obscure in logs
	$secpaths = ['rootpath' => $rootpath];

	// logger setup
	$fs = \fenrir\Filesystem::instance();
	$logger = \hlin\FileLogger::instance($fs, $logspath, $secpaths);

	// configuration reader
	$filemap = \freia\Filemap::instance($autoloader);
	$configs = \freia\Configs::instance($fs, [ $filemap ]);

	// main context
	$context = \fenrir\Context::instance($fs, $logger, $configs);

	// paths
	$context->addpath('rootpath', $rootpath);
	$context->addpath('logspath', $logspath);
	$context->addpath('cachepath', $cachepath);

	// versions

	$mainauthor = $pkg['authors'][0]['name'];
	$context->addversion($pkg['name'], $pkg['version'], $mainauthor);
	$context->setmainversion($pkg['name']);
	$context->addversion('PHP', phpversion(), 'The PHP Group');

	// special handlers
	$context->filemap_is($filemap);
	$context->autoloader_is($autoloader);

	return $context;
}
