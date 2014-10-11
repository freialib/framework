<?php namespace ran\tools;

use \hlin\attribute\Configurable;

/**
 * @copyright (c) 2014, freia Team
 * @license BSD-2 <http://freialib.github.io/license.txt>
 * @package freia Library
 */
class HTML implements Configurable {

	use \hlin\ConfigurableTrait;

	/**
	 * @return static
	 */
	static function instance(\hlin\archetype\Configs $confs) {
		$i = new static;
		$i->confs_are($confs);
		return $i;
	}

	/**
	 * @return \ran\HTMLTag
	 */
	function anchor($tagbody, $href = null) {
		return \ran\HTMLTag::i($this->confs, 'a', $tagbody)->set('href', $href);
	}

	/**
	 * @return \ran\Form
	 */
	function form($action, $standard = null) {
		return \ran\Form::i($this->confs, $standard, $action)
			->set('method', 'POST');
	}

	/**
	 * @return \ran\Form
	 */
	function queryform($action = '', $standard = null) {
		return \ran\Form::i($this->confs, $standard, $action)
			->tagbody_is('')
			->set('method', 'GET')
			->disable_metainfo();
	}

	/**
	 * @return \ran\Form
	 */
	function formfield($standard) {
		return \ran\Form::i($this->confs, $standard, null)
			->tagbody_is('')
			->set('method', 'GET')
			->disable_metainfo()
			->unsigned();
	}

	/**
	 * @return int;
	 */
	static function tabindex() {
		return 0;
	}

} # class
