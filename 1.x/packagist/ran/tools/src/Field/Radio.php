<?php namespace ran\tools;

/**
 * @copyright (c) 2014, freia Team
 * @license BSD-2 <http://freialib.github.io/license.txt>
 * @package freia Library
 */
class RadioField extends \ran\Field {

	use \ran\SwitchFieldTrait;

	/**
	 * @return static
	 */
	static function instance(\hlin\archetype\Configs $confs) {
		$i = parent::instance($confs);
		$i->set('type', 'radio');
		return $i;
	}

	/**
	 * This helper will run once. Classes that overwrite render should call this
	 * method before performing calculations.
	 */
	protected function autocompletefield() {
		if ( ! $this->autocompleted) {
			$fieldname = $this->get('name', null);
			if ($fieldname !== null && ($autovalue = $this->form->autovalue($fieldname)) !== null) {
				if ($this->get('value', false) == $autovalue) {
					$this->checked();
				}
			}
			$this->autocompleted = true;
		}
	}

} # class
