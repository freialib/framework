<?php namespace ran\tools;

/**
 * @copyright (c) 2014, freia Team
 * @license BSD-2 <http://freialib.github.io/license.txt>
 * @package freia Library
 */
class HH {

	/**
	 * @var string
	 */
	protected static $currentheader = null;

	/**
	 * ...
	 */
	static function base($headerunit) {
		static::$currentheader = $headerunit;
	}

	/**
	 * @return string
	 */
	static function next() {
		return static::$currentheader = static::raise(static::$currentheader);
	}

	/**
	 * @return string or null if no next was never called
	 */
	static function currentheader() {
		return static::$currentheader;
	}

	/**
	 * null leads to h1 and h6 leads into h6
	 *
	 * @return string next header unit
	 */
	static function raise($headerunit) {
		if ($headerunit === null) {
			return 'h1';
		}
		else { # headerunit !== null
			$index = 1 + intval(ltrim($headerunit, 'h'));

			if ($index > 6) {
				return 'h6';
			}
			else { # $index < 6
				return "h$index";
			}
		}
	}

} # class

