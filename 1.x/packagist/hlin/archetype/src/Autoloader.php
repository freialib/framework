<?php namespace hlin\archetype;

/**
 * @copyright (c) 2014, freia Team
 * @license BSD-2 <http://freialib.github.io/license.txt>
 * @package freia Library
 */
interface Autoloader {

	/**
	 * @param string symbol (class, interface, traits, etc)
	 * @param boolean autoload while checking?
	 * @return boolean symbol exists
	 */
	function exists($symbol, $autoload = false);

	/**
	 * @return boolean
	 */
	function load($symbol_name);

	/**
	 * @return array
	 */
	function paths();

	/**
	 * @return boolean success?
	 */
	function register($as_primary_autoloader = true);

	/**
	 * @return boolean success?
	 */
	function unregister();

} # interface
