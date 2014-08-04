<?php namespace fenrir\tools;

/**
 * @copyright (c) 2014, freia Team
 * @license BSD-2 <http://freialib.github.io/license.txt>
 * @package freia Library
 */
class SqlNormalizePdx implements PdxMethodSignature {

	use \fenrir\SqlMethodPdxTrait;

	/**
	 * ...
	 */
	function process(array $handlers, array & $state) {

		if ( ! isset($handlers['normalize'])) {
			return;
		}

		if (\is_callable($handlers['normalize'])) {
			$handlers['normalize']($this->db, $state);
		}
		else { // undefined behavior
			throw new Panic('Unknown format for normalize step.');
		}
	}

} # class
