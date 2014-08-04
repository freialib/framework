<?php namespace ran\tools;

/**
 * @copyright (c) 2014, freia Team
 * @license BSD-2 <http://freialib.github.io/license.txt>
 * @package freia Library
 */
class TextareaField extends \ran\Field {

	/**
	 * @return static
	 */
	static function instance(\hlin\archetype\Configs $confs) {
		$i = parent::instance($confs);
		$i->tagname_is('textarea');
		$i->tagbody_is('');
		return $i;
	}

	/**
	 * ...
	 */
	function value_is($fieldvalue) {
		$this->tagbody_is($fieldvalue);
		return $this;
	}

} # class
