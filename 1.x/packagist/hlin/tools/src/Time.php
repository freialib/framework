<?php namespace hlin\tools;

/**
 * @copyright (c) 2014, freia Team
 * @license BSD-2 <http://freialib.github.io/license.txt>
 * @package freia Library
 */
class Time {

	/**
	 * @return string
	 */
	static function timezoneOffset($timezone = null) {

		if ($timezone == null) {
			// assume system default timezone
			$now = new \DateTime('now');
		}
		else { // timezone provided
			$tz = new \DateTimeZone($timezone);
			$now = new \DateTime('now', $tz);
		}

		$mins = $now->getOffset() / 60;
		$sgn = ($mins < 0 ? -1 : 1);
		$mins = abs($mins);
		$hrs = floor($mins / 60);
		$mins -= $hrs * 60;

		return sprintf('%+d:%02d', $hrs * $sgn, $mins);
	}

} # class
