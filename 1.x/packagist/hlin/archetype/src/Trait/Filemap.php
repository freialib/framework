<?php namespace hlin\archetype;

/**
 * @copyright (c) 2014, freia Team
 * @license BSD-2 <http://freialib.github.io/license.txt>
 * @package freia Library
 */
trait FilemapTrait {

	/**
	 * @var array
	 */
	protected $filefilters = [];

	/**
	 * @return mixed contents or null
	 */
	function processmetatype(\hlin\archetype\Filesystem $fs, $type, $path) {
		return static::$filefilters[$type]($fs, $path, $paths);
	}

	/**
	 * @return boolean
	 */
	function understands($metatype) {
		return isset(static::$filefilters[$metatype]);
	}

	/**
	 * The filefilter should have the following signature
	 * 	(\hlin\archetype\Filesystem $fs, $type, array $paths)
	 */
	function register($metatype, callable $filefilter) {
		static::$filefilters[$metatype] = $filefilter;
	}

} # trait
