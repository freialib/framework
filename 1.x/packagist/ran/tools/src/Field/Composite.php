<?php namespace ran\tools;

/**
 * @copyright (c) 2014, freia Team
 * @license BSD-2 <http://freialib.github.io/license.txt>
 * @package freia Library
 */
class CompositeField extends \ran\Field {

	/**
	 * @var array
	 */
	protected $compositefields = null;

	/**
	 * @return static $this
	 */
	function addfield(\ran\Field $field) {
		$field->noerrors();
		$this->compositefields[] = $field;
		return $this;
	}

	/**
	 * @return string
	 */
	function fieldrender() {

		$renders = [];
		if ($this->compositefields !== null) {
			foreach ($this->compositefields as $idx => $field) {
				$renders['%'.($idx+1)] = $field->fieldrender();
			}
		}

		if ($this->fieldmix !== null) {
			return strtr($this->fieldmix, $renders);
		}
		else { # no fieldmix; assume concatanation of all fields
			return implode(' ', $renders);
		}
	}

	/**
	 * @var array
	 */
	protected $fieldmix = null;

	/**
	 * @return static $this
	 */
	function value_is($fieldvalue) {
		return $this; // do nothing
	}

	/**
	 * @return static $this
	 */
	function errors() {
		// start with errors set explicitly on field
		$errors = $this->errors;

		if ($errors == null) {
			$errors = [];
		}

		// add the errors in all the fields
		if ($this->compositefields !== null) {
			foreach ($this->compositefields as $field) {
				$fielderrors = $field->errors();
				if ($fielderrors !== null) {
					$errors = \hlin\Arr::join($errors, $fielderrors);
				}
			}
		}

		return $errors;
	}

	/**
	 * A template by which to mix the fields togheter. The template accepts
	 * %X entries where X is the index of the field (based on order they were
	 * introduced).
	 *
	 * eg.
	 *
	 *		$f->composite
	 *			(
	 *				'Date & Time'
	 *				[ 'datefield' => 'date', 'timefield' => 'time' ]
	 *			)
	 *			->fieldmix('%1 at %2 o'clock');
	 *
	 * @return static $this
	 */
	function fieldmix($fieldmix) {
		$this->fieldmix = $fieldmix;
		return $this;
	}

} # class
