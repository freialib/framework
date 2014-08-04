<?php namespace hlin\attribute;

/**
 * @copyright (c) 2014, freia Team
 * @license BSD-2 <http://freialib.github.io/license.txt>
 * @package freia Library
 */
interface Contextual {

	# public $context;

	/**
	 * @return static $this
	 */
	function context_is(\hlin\archetype\Context $context);

} # interface
