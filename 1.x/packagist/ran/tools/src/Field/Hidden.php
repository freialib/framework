<?php namespace ran\tools;

/**
 * @copyright (c) 2014, freia Team
 * @license BSD-2 <http://freialib.github.io/license.txt>
 * @package freia Library
 */
class HiddenField extends \ran\Field {

	/**
	 * @return static
	 */
	static function instance(\hlin\archetype\Configs $confs) {
		$i = parent::instance($confs);
		$i->tagname_is('input');
		$i->set('type', 'hidden');
		$i->disable_autocomplete();
		return $i;
	}

	/**
	 * @return static $this
	 */
	function fieldtemplate_is($template) {

		// this is a hidden field it doesn't need a template and the markup
		// might very well get in the way

		return $this;
	}

} # class
