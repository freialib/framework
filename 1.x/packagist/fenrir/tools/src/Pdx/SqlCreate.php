<?php namespace fenrir\tools;

/**
 * @copyright (c) 2014, freia Team
 * @license BSD-2 <http://freialib.github.io/license.txt>
 * @package freia Library
 */
class SqlCreatePdx implements PdxMethodSignature {

	use \fenrir\SqlMethodPdxTrait;

	/**
	 * ...
	 */
	function process(array $handlers, array & $state) {

		if ( ! isset($handlers['create:tables'])) {
			return;
		}

		if (is_array($handlers['create:tables'])) {
			$total_tables = count($handlers['create:tables']);
			$done_tables = 0;
			$state['progress.writer']($done_tables, $total_tables);

			foreach ($handlers['create:tables'] as $table => $def) {
				try {
					if (is_string($def)) {
						\fenrir\SqlPdx::create_table
							(
								$this->db, $table,
								$def,
								$state['sql']['constants'],
								$state['sql']['default']['engine'],
								$state['sql']['default']['charset']
							);
					}
					else if (is_array($def)) {
						\fenrir\SqlPdx::create_table
							(
								$this->db, $table,
								$def['definition'],
								$state['sql']['constants'],
								$def['engine'],
								$def['charset']
							);
					}
					else if (is_callable($def)) {
						$def($this->db, $state);
					}
					else { // unknown format
						throw new Panic('Unsupported format for definition in tables step.');
					}
				}
				catch (\Exception $e) {
					$writer = $state['writer'];
					$writer("\nException while running [tables] migration operation for [{$table}].\n\n");
					$writer("Definition:\n\n%s\n\n", \hlin\Text::reindent($def));
					throw $e;
				}

				$done_tables += 1;
				$state['progress.writer']($done_tables, $total_tables);
			}
		}
		else if (is_callable($handlers['create:tables'])) {
			$handlers['create:tables']($this->db, $state);
		}
		else { // undefined behavior
			throw new Panic('Unknown format for create step.');
		}
	}

} # class
