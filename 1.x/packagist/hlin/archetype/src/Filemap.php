<?php namespace hlin\archetype;

/**
 * @copyright (c) 2014, freia Team
 * @license BSD-2 <http://freialib.github.io/license.txt>
 * @package freia Library
 */
interface Filemap {

	/**
	 * @return mixed contents or null
	 */
	function file(Filesystem $fs, $path, $metatype = null);

	/**
	 * @return mixed contents or null
	 */
	function processmetatype(\hlin\archetype\Filesystem $fs, $type, $path);

	/**
	 * @return boolean
	 */
	function understands($metatype);

	/**
	 * The filefilter parameter should have the following signature
	 * 	(\hlin\archetype\Filesystem $fs, $type, array $paths)
	 */
	function register($metatype, callable $filefilter);

} # interface
