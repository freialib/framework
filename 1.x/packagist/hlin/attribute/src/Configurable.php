<?php namespace hlin\attribute;

/**
 * @copyright (c) 2014, freia Team
 * @license BSD-2 <http://freialib.github.io/license.txt>
 * @package freia Library
 */
interface Configurable {

	# public $confs;

	/**
	 * @return static $this
	 */
	function confs_are(\hlin\archetype\Configs $confs);

} # interface
