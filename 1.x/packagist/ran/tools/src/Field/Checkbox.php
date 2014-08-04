<?php namespace ran\tools;

/**
 * @copyright (c) 2014, freia Team
 * @license BSD-2 <http://freialib.github.io/license.txt>
 * @package freia Library
 */
class CheckboxField extends \ran\Field {

	use \ran\SwitchFieldTrait;

	#
	# Checkbox intentionally doesn't inherit Radio
	#

	/**
	 * @return static
	 */
	static function instance(\hlin\archetype\Configs $confs) {
		$i = parent::instance($confs);
		$i->set('type', 'checkbox');
		$i->value_is(1); # default value sent to server when checked
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

				#
				# The checkbox is simply a switch for a value. So if the
				# autocomplete value is equal to the field's current value then
				# the field is checked. Otherwise the field is unchecked.
				#

				$value = $this->get('value', 'freia:no-value');
				if ($value !== 'freia:no-value') {
					if ($value == $autovalue) {
						$this->checked();
					}
					else { # autovalue != value && autovalue !== null
						$this->unchecked();
					}
				}
			}

			$this->autocompleted = true;
		}
	}

} # class
