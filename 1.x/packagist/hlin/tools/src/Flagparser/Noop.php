<?php namespace hlin\tools;

/**
 * @copyright (c) 2014, freia Team
 * @license BSD-2 <http://freialib.github.io/license.txt>
 * @package freia Library
 */
class NoopFlagparser implements \hlin\tools\FlagparserSignature {

	use \hlin\FlagparserTrait;

	/**
	 * @return mixed
	 */
	function parse($args) {
		return $args;
	}

	/**
	 * @return string|boolean help or false on no parameter help required
	 */
	function consoleHelp($command, $conf) {
		if ( ! isset($conf['help'])) {
			return ' This command does not provide any parameter help.';
		}
		else { // custom help available

			if ($conf['help'] === false) {
				return false;
			}

			return strtr
				(
					$conf['help'],
					[
						':invokation' => $this->cli->syscall().' '.$command
					]
				);
		}
	}

} # class
