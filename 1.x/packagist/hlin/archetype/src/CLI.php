<?php namespace hlin\archetype;

/**
 * @copyright (c) 2014, freia Team
 * @license BSD-2 <http://freialib.github.io/license.txt>
 * @package freia Library
 */
interface CLI {

	/**
	 * @codeCoverageIgnore
	 * @see http://php.net/manual/en/function.passthru.php
	 */
	function passthru($command, &$return_var = null);

	/**
	 * @codeCoverageIgnore
	 * @see http://php.net/manual/en/function.system.php
	 * @return string
	 */
	function system($command, &$return_var = null);

	/**
	 * @codeCoverageIgnore
	 * @see http://php.net/manual/en/function.exec.php
	 * @return string
	 */
	function exec($command, array &$output = null, &$return_var = null);

	/**
	 * ...
	 */
	function echoOff();

	/**
	 * ...
	 */
	function echoOn();

	/**
	 * ...
	 */
	function printf($format);

	/**
	 * ...
	 */
	function printf_error($format);

	/**
	 * @return string
	 */
	function fgets();

	/**
	 * Shorthand for retrieving confirmation from console. Common console task
	 * for input.
	 *
	 * @return string answer
	 */
	function ask($question, array $options);

	/**
	 * @return array
	 */
	function args();

	/**
	 * @return string
	 */
	function syscall();

	/**
	 * @return string
	 */
	function command();

	/**
	 * @return array
	 */
	function flags();

} # interface
