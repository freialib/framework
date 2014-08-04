<?php namespace hlin\tools;

/**
 * @copyright (c) 2014, freia Team
 * @license BSD-2 <http://freialib.github.io/license.txt>
 * @package freia Library
 */
interface RequestSignature extends \hlin\attribute\Contextual {

	/**
	 * @return string
	 */
	function requestUri();

	/**
	 * @return string
	 */
	function requestMethod();

	/**
	 * Given a set of acceptable formats the system tries to retrieve the
	 * data format specified and return it in the specified output format.
	 *
	 * @return mixed input
	 */
	function input(array $formats, $output = 'array');

	/**
	 * @return static $this
	 */
	function params_are($params);

	/**
	 * @return mixed
	 */
	function param($name);

	/**
	 * @return array
	 */
	function params();

} # signature
