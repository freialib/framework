<?php namespace fenrir\tools;

/**
 * @copyright (c) 2014, freia Team
 * @license BSD-2 <http://freialib.github.io/license.txt>
 * @package freia Library
 */
class SqlPopulatePdx implements PdxMethodSignature {

	use \fenrir\SqlMethodPdxTrait;

	/**
	 * ...
	 */
	function process(array $handlers, array & $state) {

		if ( ! isset($handlers['populate'])) {
			return;
		}

		if (\is_callable($handlers['populate'])) {
			$handlers['populate']($this->db, $state);
		}
		else { // undefined behavior
			throw new Panic('Unknown format for populate step.');
		}
	}

} # class
