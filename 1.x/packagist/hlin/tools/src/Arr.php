<?php namespace hlin\tools;

/**
 * @copyright (c) 2014, freia Team
 * @license BSD-2 <http://freialib.github.io/license.txt>
 * @package freia Library
 */
class Arr {

	/**
	 * PHP join with a callback
	 *
	 * This is slightly different from implode($glue, array_map) in that it can
	 * ignore entries if the entry in question passes false out.
	 *
	 * @return string
	 */
	static function join($glue, array $list, callable $manipulator) {
		$glued = '';
		foreach ($list as $key => $value) {
			$item = $manipulator($key, $value);
			if ($item !== false) {
				$glued .= $glue.$item;
			}
		}

		if ( ! empty($glued)) {
			$glued = substr($glued, strlen($glue));
		}

		return $glued;
	}

} # class
