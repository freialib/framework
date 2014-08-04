<?php namespace fenrir\tools;

/**
 * Collection of helper function to use when creating Pdx migrations
 *
 * @copyright (c) 2014, freia Team
 * @license BSD-2 <http://freialib.github.io/license.txt>
 * @package freia Library
 */
class SqlPdx {

// ---- Basic Operations ------------------------------------------------------

	/**
	 * Performs safe select of entries.
	 *
	 * @return array entries
	 */
	static function select(\fenrir\system\MysqlDatabaseSignature $db, $table, $constraints = null) {
		$constraints !== null or $constraints = '1';
		return $db->prepare
			(
				"
					SELECT *
					  FROM `$table`
					 WHERE $constraints
				"
			)
			->execute()
			->fetch_all();
	}

	/**
	 * Performs safe insert into table given values and keys. This is a very
	 * primitive function, which gurantees the integrity of the operation
	 * inside the migration.
	 *
	 * Do not use api powered insertion commands since they will break as the
	 * source code changes. Since the migration gurantees the integrity of the
	 * api commands, the migration can not rely on them, since that would cause
	 * a circular dependency chain.
	 *
	 * Fortunately since insert operations in migrations are unlikely to pull
	 * any user data hardcoding them like this is very straight forward and
	 * safe.
	 *
	 * @return int ID
	 */
	static function insert(\fenrir\system\MysqlDatabaseSignature $db, $table, array $values, $map = null) {
		$map !== null or $map = [];
		isset($map['nums']) or $map['nums'] = [];
		isset($map['bools']) or $map['bools'] = [];
		isset($map['dates']) or $map['dates'] = [];

		$rawkeys = array_keys($values);
		$keys = \hlin\Arr::join(', ', $rawkeys, function ($i, $key) {
			return "`$key`";
		});
		$refs = \hlin\Arr::join(', ', $rawkeys, function ($i, $key) {
			return ":$key";
		});

		$stmt = $db->prepare("INSERT INTO `$table` ($keys) VALUES ($refs)");

		// populate statement
		foreach ($values as $key => $value) {
			if (in_array($key, $map['nums'])) {
				$stmt->num(":$key", $value);
			}
			else if (in_array($key, $map['bools'])) {
				$stmt->bool(":$key", $value);
			}
			else if (in_array($key, $map['dates'])) {
				$stmt->date(":$key", $value);
			}
			else { // assume string
				$stmt->str(":$key", $value);
			}
		}

		$stmt->execute();
		return $db->lastInsertId();
	}

	/**
	 * Same as insert only values is assumed to be array of arrays.
	 */
	static function massinsert(\fenrir\system\MysqlDatabaseSignature $db, $table, array $values, $map = null) {
		$db->begin();
		try {
			foreach ($values as $value) {
				static::insert($db, $table, $value, $map);
			}
			$db->commit();
		}
		catch (\Exception $e) {
			$db->rollback();
			throw $e;
		}
	}


// ---- Structure Changes -----------------------------------------------------

	/**
	 * ...
	 */
	static function create_table(\fenrir\system\MysqlDatabaseSignature $db, $table, $definition, array $constants, $engine = 'InnoDB', $charset = 'utf8') {
		$constants = $constants + [
			'table' => $table,
			'engine' => $engine,
			'default_charset' => $charset
		];

		$db->prepare
			(
				"
					CREATE TABLE `[table]`
					(
						$definition
					)
					ENGINE=[engine]
					DEFAULT CHARSET=[default_charset]
				",
				$constants
			)
			->execute();
	}

	/**
	 * Remove specified bindings.
	 */
	static function remove_bindings(\fenrir\system\MysqlDatabaseSignature $db, $table, array $bindings) {
		foreach ($bindings as $key) {
			$db->prepare
				(
					"
						ALTER TABLE `$table`
						 DROP FOREIGN KEY `$key`
					"
				)
				->execute();
		}
	}

// ---- Utility ---------------------------------------------------------------

	/**
	 * When converting from one database structure to another it is often
	 * required to translate one structure to another, which involves going
	 * though all the entries in a central table; this method abstracts the
	 * procedure for you.
	 *
	 * Batch reads with batch commits for changes is generally the fastest way
	 * to perform the operations.
	 */
	static function processor(\fenrir\system\MysqlDatabaseSignature $db, $table, $count, $callback, $reads = 1000) {
		$pages = ((int) ($count / $reads)) + 1;

		for ($page = 1; $page <= $pages; ++$page) {
			$db->begin();
			$entries = $db->prepare
				(
					"
						SELECT *
						  FROM `$table`
						 LIMIT :limit OFFSET :offset
					"
				)
				->page($page, $reads)
				->execute()
				->fetch_all();

			foreach ($entries as $entry) {
				$callback($db, $entry);
			}
			$db->commit();
		}
	}

} # class
