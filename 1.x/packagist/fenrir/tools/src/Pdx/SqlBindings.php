<?php namespace fenrir\tools;

/**
 * @copyright (c) 2014, freia Team
 * @license BSD-2 <http://freialib.github.io/license.txt>
 * @package freia Library
 */
class SqlBindingsPdx implements PdxMethodSignature {

	use \fenrir\SqlMethodPdxTrait;

	/**
	 * ...
	 */
	function process(array $handlers, array & $state) {

		if ( ! isset($handlers['bindings'])) {
			return;
		}

		$db = $this->db;
		if (is_array($handlers['bindings'])) {
			$total_tables = count($handlers['bindings']);
			$done_tables = 0;
			$state['progress.writer']($done_tables, $total_tables);

			foreach ($handlers['bindings'] as $table => $constraints) {
				$query = "ALTER TABLE `$table` ";

				$idx = 0;
				$count = count($constraints);
				foreach ($constraints as $key => $constraint) {
					++$idx;

					if ( ! isset($constraint[3])) {
						$constraint_key = $key;
					}
					else { // constraint key set
						$constraint_key = $constraint[3];
					}

					// keys must be unique over the whole database
					$constraint_key = $table.'_'.$constraint_key;

					$query .=
						"
							ADD CONSTRAINT `$constraint_key`
							   FOREIGN KEY (`$key`)
								REFERENCES `{$constraint[0]}` (`id`)
								 ON DELETE {$constraint[1]}
								 ON UPDATE {$constraint[2]}
						";

					if ($idx < $count) {
						$query .= ', ';
					}
					else { // last element
						$query .= ';';
					}
				}

				$db->prepare($query)->execute();
				$done_tables += 1;
				$state['progress.writer']($done_tables, $total_tables);
			}
		}
		else if (is_callable($handlers['bindings'])) {
			$handlers['bindings']($db, $state);
		}
		else { // undefined behavior
			throw new Panic('Unknown format for bindings step.');
		}
	}

} # class
