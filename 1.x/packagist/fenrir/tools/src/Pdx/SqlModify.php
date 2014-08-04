<?php namespace fenrir\tools;

/**
 * @copyright (c) 2014, freia Team
 * @license BSD-2 <http://freialib.github.io/license.txt>
 * @package freia Library
 */
class SqlModifyPdx implements PdxMethodSignature {

	use \fenrir\SqlMethodPdxTrait;

	/**
	 * ...
	 */
	function process(array $handlers, array & $state) {

		if ( ! isset($handlers['modify:tables'])) {
			return;
		}

		if (is_array($handlers['modify:tables'])) {
			$total_tables = count($handlers['modify:tables']);
			$done_tables = 0;
			$state['progress.writer']($done_tables, $total_tables);

			$constants = $state['sql']['constants'];
			foreach ($handlers['modify:tables'] as $table => $def) {
				try {
					$this->db->prepare
						(
							"
								ALTER TABLE `$table`
								$def
							",
							$constants
						)
						->execute();
				}
				catch (\Exception $e) {
					$writer = $state['writer'];
					$writer("\nException while running [modify] migration operation for [{$table}].\n\n");
					$writer("Definition:\n\n%s\n\n", \app\Text::reindent($def));
					throw $e;
				}

				$done_tables += 1;
				$state['progress.writer']($done_tables, $total_tables);
			}
		}
		else if (is_callable($handlers['modify:tables'])) {
			$handlers['modify:tables']($this->db, $state);
		}
		else { // undefined behavior
			throw new Panic('Unknown format for modify step.');
		}
	}

} # class
