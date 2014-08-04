<?php namespace fenrir\tools;

/**
 * @copyright (c) 2014, freia Team
 * @license BSD-2 <http://freialib.github.io/license.txt>
 * @package freia Library
 */
class SqlCleanupPdx implements PdxMethodSignature {

	use \fenrir\SqlMethodPdxTrait;

	/**
	 * ...
	 */
	function process(array $handlers, array & $state) {

		if ( ! isset($handlers['cleanup:tables'])) {
			return;
		}

		if (is_array($handlers['cleanup:tables'])) {
			if (isset($handlers['cleanup:tables']['bindings'])) {
				foreach ($handlers['cleanup:tables']['bindings'] as $table => $constraints) {
					\fenrir\SqlPdx::remove_bindings($this->db, $table, $constraints);
				}
			}
		}
		else if (is_callable($handlers['cleanup:tables'])) {
			$handlers['cleanup:tables']($this->db, $state);
		}
		else { // undefined behavior
			throw new Panic('Unknown format for cleanup step.');
		}
	}

} # class
