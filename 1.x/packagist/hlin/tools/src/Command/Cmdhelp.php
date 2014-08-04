<?php namespace hlin\tools;

/**
 * @copyright (c) 2014, freia Team
 * @license BSD-2 <http://freialib.github.io/license.txt>
 * @package freia Library
 */
class CmdhelpCommand implements \hlin\archetype\Command {

	use \hlin\CommandTrait;

	/**
	 * @return int
	 */
	function main(array $args = null) {

		$cmd = array_shift($args);

		if (empty($cmd)) {
			$cmd = '?';
		}

		$commands = $this->confs->read('freia/commands');

		if ( ! isset($commands[$cmd])) {
			$this->cli->printf(" The command $cmd is not defined.");
			return 500;
		}
		else { // attempt to print command help information

			$command = $commands[$cmd];

			// print description
			if ( ! isset($command['desc']) || empty($command['desc'])) {
				if (isset($command['summary'])) {
					$command['desc'] = ucfirst($command['summary']);
				}
				else { // missing summary too
					$command['desc'] = 'No documentation could be found for command.';
				}
			}
			else { // normalize desc
				$command['desc'] = rtrim($command['desc'], "\n\r ");
			}

			$this->cli->printf("\n");
			$this->printp($command['desc'], 2);

			if ( ! isset($command['flagparser'])) {
				$command['flagparser'] = 'hlin.NoopFlagparser';
			}

			$class = \hlin\PHP::pnn($command['flagparser']);

			if ( ! class_exists($class)) {
				throw new Panic("Missing flag parser class $class");
			}

			$parser = $class::instance($this->context);
			$console_help = $parser->consoleHelp($cmd, $command);

			if ($console_help !== false) {
				$this->cli->printf("\n");
				$this->delimiter();
				$this->cli->printf(rtrim($console_help, "\n\r "));
				$this->cli->printf("\n\n");
			}

			$this->delimiter();

			$this->cli->printf("\n USAGE EXAMPLES\n\n");
			$this->cli->printf($parser->consoleExamples($cmd, $command));
			$this->cli->printf("\n");

			return 0;
		}
	}

// ---- Private ---------------------------------------------------------------

	/**
	 * Print paragraph. Anything passed will be trimmed and printed so as to fit
	 * in the limit of 75 characters per line.
	 */
	protected function printp($input, $indent = 0) {
		$input = trim($input, "\n\t\r ");
		$indentation = str_repeat(' ', $indent);
		$this->cli->printf($indentation.wordwrap(str_replace("\n", "\n$indentation", $input), 75 - $indent, "\n$indentation")."\n\n");
	}

	/**
	 * ...
	 */
	function delimiter() {
		$this->cli->printf(str_repeat('-', 75)."\n");
	}

} # class
