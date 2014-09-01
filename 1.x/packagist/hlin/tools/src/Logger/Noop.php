<?php namespace hlin\tools;

/**
 * @copyright (c) 2014, freia Team
 * @license BSD-2 <http://freialib.github.io/license.txt>
 * @package freia Library
 */
class NoopLogger implements \hlin\archetype\Logger {

	use \hlin\LoggerTrait;

	/**
	 * @return static
	 */
	static function instance() {
		$i = new static;
		return $i;
	}

	/**
	 * Logs a message. The type can be used as a hint to the logger on how to
	 * log the message. If the logger doesn't understand the type a file with
	 * the type name will be created as default behavior.
	 *
	 * Types should not use illegal file characters.
	 */
	function log($message, $type = null, $explicit = false) {
		// empty
	}

	function logexception(\Exception $exception) {
		// empty
	}

	/**
	 * Save the data passed in as data.
	 */
	function var_dump($message, $data) {
		// empty
	}

} # class
