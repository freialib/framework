<?php namespace hlin\archetype;

/**
 * @copyright (c) 2014, freia Team
 * @license BSD-2 <http://freialib.github.io/license.txt>
 * @package freia Library
 */
interface Command extends \hlin\attribute\Contextual {

	/**
	 * @return int
	 */
	function main(array $args = null);

} # interface
