<?php namespace example\system;

/**
 * ...
 */
class Todo implements \hlin\archetype\Model {

	use \hlin\ModelTrait;

	/**
	 * @return static
	 */
	static function instance(array $data) {
		$i = new static;
		$i->attrs = $data;
		return $i;
	}

} # class
