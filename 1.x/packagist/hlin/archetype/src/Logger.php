<?php namespace hlin\archetype;

/**
 * @copyright (c) 2014, freia Team
 * @license BSD-2 <http://freialib.github.io/license.txt>
 * @package freia Library
 */
interface Logger {

	/**
	 * Logs an exception.
	 */
	function logexception(\Exception $exception);
	# implementations should always log exception stackraces

	/**
	 * Logs a message. The type can be used as a hint to the logger on how to
	 * log the message. If the logger doesn't understand the type a file with
	 * the type name will be created as default behavior.
	 *
	 * Types should not use illegal file characters.
	 */
	function log($message, $type = null);
	# type files should not be re-located into year/month structures

	/**
	 * Save the data passed in as data.
	 */
	function var_dump($message, $data);
	# how exactly this is presented may depend on logger

} # interface
