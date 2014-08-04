<?php namespace hlin\archetype;

/**
 * @copyright (c) 2014, freia Team
 * @license BSD-2 <http://freialib.github.io/license.txt>
 * @package freia Library
 */
trait ModelTrait {

	/**
	 * @var array
	 */
	protected $attrs = [];

	/**
	 * @return array
	 */
	function toArray() {
		return $this->attrs;
	}

} # trait
