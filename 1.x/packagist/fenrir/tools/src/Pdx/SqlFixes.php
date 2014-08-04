<?php namespace fenrir\tools;

/**
 * @copyright (c) 2014, freia Team
 * @license BSD-2 <http://freialib.github.io/license.txt>
 * @package freia Library
 */
class SqlFixesPdx implements PdxMethodSignature {

	use \fenrir\SqlMethodPdxTrait;

	/**
	 * ...
	 */
	function process(array $handlers, array & $state) {

		if ( ! isset($handlers['fixes'])) {
			return;
		}

		if (\is_callable($handlers['fixes'])) {
			$handlers['fixes']($this->db, $state);
		}
		else { // undefined behavior
			throw new Panic('Unknown format for fixes step.');
		}
	}

} # class
