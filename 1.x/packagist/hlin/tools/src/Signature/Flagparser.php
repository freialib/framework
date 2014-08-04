<?php namespace hlin\tools;

/**
 * @copyright (c) 2014, freia Team
 * @license BSD-2 <http://freialib.github.io/license.txt>
 * @package freia Library
 */
interface FlagparserSignature extends \hlin\attribute\Contextual {

	/**
	 * @return mixed
	 */
	function parse($args);

	/**
	 * @return string
	 */
	function consoleHelp($command, $conf);

	/**
	 * @return string|boolean help or false on no parameter help required
	 */
	function consoleExamples($command, $conf);

} # signature
