<?php namespace hlin\tools;

/**
 * @copyright (c) 2014, freia Team
 * @license BSD-2 <http://freialib.github.io/license.txt>
 * @package freia Library
 */
class PHP {

	/**
	 * Universal Namespace Name
	 * Accepts both classes, classes with namespace, etc.
	 * Also fixes nonsense from PHP, such as double slashes in namespace.
	 *
	 * @return string normalized namespace
	 */
	static function unn($symbol) {
		return trim(preg_replace('#[^a-zA-Z0-9_]#', '.', $symbol), '.');
	}

	/**
	 * PHP Namespace Name
	 * Accepts both classes, classes with namespace, etc.
	 *
	 * @return string from unn back to php
	 */
	static function pnn($symbol) {
		return str_replace('.', '\\', $symbol);
	}

} # class
