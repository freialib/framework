<?php namespace example\linker;

/**
 * ...
 */
class ThreadRepo implements \hlin\archetype\Repo {

	use \fenrir\MysqlRepoTrait;

	/**
	 * @return array
	 */
	function constants() {
		return [
			'model' => 'example.Thread',
			'table' => 'threads'
		];
	}

	/**
	 * @return static
	 */
	static function instance(\fenrir\MysqlDatabase $db) {
		$i = new static;
		$i->db = $db;
		return $i;
	}

} # class
