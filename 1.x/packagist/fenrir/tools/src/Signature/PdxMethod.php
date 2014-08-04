<?php namespace fenrir\tools;

/**
 * [!!] "Method" doesn't actually refer to "class method," but the meaning of
 * the word in general, as in "the way you do something".
 *
 * @copyright (c) 2014, freia Team
 * @license BSD-2 <http://freialib.github.io/license.txt>
 * @package freia Library
 */
interface PdxMethodSignature {

	/**
	 * ...
	 */
	function process(array $handlers, array & $state);

} # signature
