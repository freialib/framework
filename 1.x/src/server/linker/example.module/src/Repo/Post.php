<?php namespace example\linker;

/**
 * ...
 */
class PostRepo implements \hlin\archetype\Repo {

	use \fenrir\MysqlRepoTrait;

	/**
	 * @return array
	 */
	function constants() {
		return [
			'model' => 'example.Post',
			'table' => 'posts'
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
