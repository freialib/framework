<?php namespace example\linker;

/**
 * ...
 */
class TodoRepo implements \hlin\archetype\Repo {

	use \fenrir\MysqlRepoTrait;

	/**
	 * @return array
	 */
	function constants() {
		return [
			'model' => 'example.Todo',
			'table' => 'todos'
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
