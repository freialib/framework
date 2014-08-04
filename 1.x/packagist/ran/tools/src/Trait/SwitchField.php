<?php namespace ran\tools;

/**
 * @copyright (c) 2014, freia Team
 * @license BSD-2 <http://freialib.github.io/license.txt>
 * @package freia Library
 */
trait SwitchFieldTrait {

	/**
	 * @return static $this
	 */
	function checked() {
		$this->set('checked', '');
		return $this;
	}

	/**
	 * @return static $this;
	 */
	function unchecked() {
		$this->set('checked', null);
		return $this;
	}

	/**
	 * @return static $this
	 */
	function checked_state($state) {
		if ($state) {
			$this->checked();
		}
		else { # checked
			$this->unchecked();
		}

		return $this;
	}

} # trait
