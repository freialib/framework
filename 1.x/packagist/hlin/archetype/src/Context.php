<?php namespace hlin\archetype;

/**
 * @copyright (c) 2014, freia Team
 * @license BSD-2 <http://freialib.github.io/license.txt>
 * @package freia Library
 */
interface Context {

// ---- System Information ----------------------------------------------------

	# public $logger;
	# public $web;
	# public $confs;
	# public $cli;
	# public $fs;

	/**
	 * @codeCoverageIgnore
	 * @see http://php.net/manual/en/function.php-sapi-name.php
	 * @return string
	 */
	function php_sapi_name();

// ---- Logger ----------------------------------------------------------------

	/**
	 * @return static $this
	 */
	function logger_is(Logger $logger);

	/**
	 * @return static $this
	 */
	function web_is(Web $web);

	/**
	 * @return static $this
	 */
	function confs_is(Configs $confs);

	/**
	 * @return static $this
	 */
	function filesystem_is(Filesystem $filesystem);

	/**
	 * @return static $this
	 */
	function cli_is(CLI $cli);

// ---- Named Paths -----------------------------------------------------------

	/**
	 * Registers a path under a specified name.
	 *
	 * If the path is already registered the method will Panic unless the 3rd
	 * parameter is passed.
	 */
	function addpath($name, $fspath, $overwrite = false);

	/**
	 * Retrieves the specified path. If the path is not registered the method
	 * will Panic, unless the second parameter is set, in which case it will
	 * just return null.
	 *
	 * @return string|null
	 */
	function path($name, $null_on_failure = false);

// ---- Special Options -------------------------------------------------------

	/**
	 * System file mapping.
	 */
	function filemap_is(\hlin\archetype\Filemap $filemap);

	/**
	 * @return \hlin\archetype\Filemap
	 */
	function filemap();

	/**
	 * System file mapping.
	 */
	function autoloader_is(\hlin\archetype\Autoloader $autoloader);

	/**
	 * @return \hlin\archetype\Autoloader
	 */
	function autoloader();

// ---- Version Information ---------------------------------------------------

	/**
	 * @return string
	 */
	function version();

	/**
	 * @return array (version, author)
	 */
	function versioninfo();

	/**
	 * @return string
	 */
	function subversions();

	/**
	 * @return static $this
	 */
	function addversion($name, $version, $author);

	/**
	 * @return static $this
	 */
	function setmainversion($name);

} # interface
