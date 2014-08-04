<?php namespace hlin\tools;

/**
 * @copyright (c) 2014, freia Team
 * @license BSD-2 <http://freialib.github.io/license.txt>
 * @package freia Library
 */
trait FlagparserTrait {

	use \hlin\ContextualTrait;

	/**
	 * @return string
	 */
	function consoleExamples($command, $conf) {
		if ( ! isset($conf['examples'])) {
			return ' This command does not provide examples.';
		}
		else { // custom help available
			$examples = [];
			$invokation = $this->cli->syscall().' '.$command;
			foreach ($conf['examples'] as $example => $details) {
				$desc = wordwrap(trim($details, "\n\r\t "), 75 - 3, "\n # ");
				$examples[] = sprintf(" # $desc\n $invokation $example");
			}

			return implode("\n\n", $examples);
		}
	}

} # trait
