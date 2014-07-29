<?php namespace app\main;

use \hlin\archetype\Autoloader;

/**
 * @return \hlin\archetype\Context
 */
function context($srcpath, $syspath, $logspath, Autoloader $autoloader) {

	// paths
	$cachepath = realpath("$syspath/app/cache");

	// paths to obscure in logs
	$secpaths = ['syspath' => $syspath];

	// logger setup
	$fs = \fenrir\Filesystem::instance();
	$logger = \hlin\FileLogger::instance($fs, $logspath, $secpaths);

	// configuration reader
	$filemap = \freia\Filemap::instance($autoloader);
	$configs = \freia\Configs::instance($fs, [ $filemap ]);

	// main context
	$context = \fenrir\Context::instance($fs, $logger, $configs);

	// paths
	$context->addpath('syspath', $syspath);
	$context->addpath('logspath', $logspath);
	$context->addpath('cachepath', $cachepath);

	// versions
	$pkg = json_decode(file_get_contents("$srcpath/composer.json"), true);
	$mainauthor = $pkg['authors'][0]['name'];
	$context->addversion($pkg['name'], $pkg['version'], $mainauthor);
	$context->setmainversion($pkg['name']);
	$context->addversion('PHP', phpversion(), 'The PHP Group');

	// special handlers
	$context->filemap_is($filemap);
	$context->autoloader_is($autoloader);

	return $context;
}
