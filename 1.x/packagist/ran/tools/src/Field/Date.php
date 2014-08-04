<?php namespace ran\tools;

/**
 * @copyright (c) 2014, freia Team
 * @license BSD-2 <http://freialib.github.io/license.txt>
 * @package freia Library
 */
class DateField extends \ran\Field {

	/**
	 * @var string
	 */
	protected $dateformat = 'Y-m-d';

	/**
	 * @return static
	 */
	static function instance(\hlin\archetype\Configs $confs) {
		$i = parent::instance($confs);
		$i->set('type', 'date');
		return $i;
	}

	/**
	 * @return static $this
	 */
	function dateformat_is($new_dateformat) {

		$value = $this->get('value');

		if ($value !== null) {
			$date = \DateTime::createFromFormat($this->dateformat, $value);
			$this->set('value', $date->format($new_dateformat));
		}

		$this->dateformat = $new_dateformat;

		return $this;
	}

	/**
	 * @return static $this
	 */
	function value_is($fieldvalue) {

		if (is_a($fieldvalue, '\DateTime')) {
			// if we don't explicitly set it, the format will be Y-m-d H:i:s
			// which won't work on this type of field
			$this->set('value', $fieldvalue->format($this->dateformat));
		}
		else { # not a DateTime object
			$this->set('value', $fieldvalue);
		}

		return $this;
	}


} # class
