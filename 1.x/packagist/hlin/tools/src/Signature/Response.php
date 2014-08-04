<?php namespace hlin\tools;

/**
 * @copyright (c) 2014, freia Team
 * @license BSD-2 <http://freialib.github.io/license.txt>
 * @package freia Library
 */
interface ResponseSignature extends \hlin\attribute\Contextual {

// ---- HTTP Headers ----------------------------------------------------------

	/**
	 * Equivalent in functionality to http_response_code from PHP
	 *
	 * @see http://www.php.net/manual/en/function.http-response-code.php
	 * @return static $this
	 */
	function responseCode($code = null);

	/**
	 * @see http://www.php.net//manual/en/function.header.php
	 */
	function header($header, $replace = true, $http_response_code = null);

	/**
	 * @return array
	 */
	function headers();

	/**
	 * Redirect type can be 303 (see other), 301 (permament), 307 (temporary)
	 */
	function redirect($url, $type = 303);

// ---- Response Logic --------------------------------------------------------

	/**
	 * Set the rendering logic to use in the final phase of processing.
	 *
	 * @return static $this
	 */
	function logic(callable $func);

	/**
	 * @return static $this
	 */
	function conf($conf);

	/**
	 * @return string
	 */
	function parse($input);

} # signature
