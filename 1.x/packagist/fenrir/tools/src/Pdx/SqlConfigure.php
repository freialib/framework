<?php namespace fenrir\tools;

/**
 * @copyright (c) 2014, freia Team
 * @license BSD-2 <http://freialib.github.io/license.txt>
 * @package freia Library
 */
class SqlConfigurePdx implements PdxMethodSignature {

	use \fenrir\SqlMethodPdxTrait;

	/**
	 * ...
	 */
	function process(array $handlers, array & $state) {

		if ( ! isset($handlers['configure'])) {
			return;
		}

		if (is_array($handlers['configure'])) {
			if (isset($handlers['configure']['tables'])) {
				foreach ($handlers['configure']['tables'] as $table) {
					if ( ! in_array($table, $state['tables'])) {
						$state['tables'][] = $table;
					}
				}
			}
		}
		else if (is_callable($handlers['configure'])) {
			$handlers['configure']($this->db, $state);
		}
		else {  // undefined behavior
			throw new Panic('Unknown format for configure step.');
		}
	}

} # class
