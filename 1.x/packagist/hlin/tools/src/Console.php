<?php namespace hlin\tools;

/**
 * @copyright (c) 2014, freia Team
 * @license BSD-2 <http://freialib.github.io/license.txt>
 * @package freia Library
 */
class Console {

	use \hlin\ContextualTrait;

	/**
	 * @return int
	 */
	function main(array $commands) {
		try {

			// translate to help in case of known synonyms
			if ($this->is_helpcommandSynonym()) {
				$command = 'help';
			}
			else if ($this->is_versioncommandSynonym()) {
				$command = 'version';
			}
			else { // not help command
				$command = $this->cli->command();
			}

			if ( ! isset($commands[$command])) {
				throw new Panic("Missing configuration for command $command");
			}

			$conf = $this->normalizeConfiguration($command, $commands[$command]);
			$class = \hlin\PHP::pnn($conf['command']);

			if ( ! class_exists($class)) {
				throw new Panic("Missing command class $class");
			}

			$cmd = $class::instance($this->context);
			return $cmd->main($this->cli->flags());;
		}
		catch (\Exception $e) {
			$this->printException($e);
			return 500;
		}
	}

// ---- Private ---------------------------------------------------------------

	/**
	 * ...
	 */
	protected function printException(\Exception $e) {
		$rootpath = $this->context->path('rootpath');
		$trace = str_replace("\n", "\n   ", "\n".$e->getTraceAsString());
		$trace = str_replace($rootpath, 'rootpath:', $trace);
		$this->cli->printf_error("\n  %s\n%s\n", $e->getMessage(), $trace);
	}

	/**
	 * @return boolean
	 */
	protected function is_helpcommandSynonym() {
		$helpflags = [ '--help', '-h' ];
		$cmd = $this->cli->command();

		return in_array($cmd, $helpflags)
			|| $this->cli->command() == null;
	}

	/**
	 * @return boolean
	 */
	protected function is_versioncommandSynonym() {
		$versionflags = [ '--version', '-v' ];
		$cmd = $this->cli->command();

		return in_array($cmd, $versionflags);
	}

	/**
	 * @return array
	 */
	protected function normalizeConfiguration($command, $dirtyconf) {

		$conf = $dirtyconf;

		if ( ! isset($conf['command'])) {
			throw new Panic("Configuration for $command command is missing required [command] parameter.");
		}

		if ( ! isset($conf['flagparser'])) {
			$conf['flagparser'] = 'hlin.NoopFlagparser';
		}

		if ( ! isset($conf['summary'])) {
			$conf['summary'] = 'no summary';
		}

		if ( ! isset($conf['desc'])) {
			$conf['desc'] = '';
		}

		if ( ! isset($conf['topic'])) {
			$conf['topic'] = '-unsorted-';
		}

		return $conf;
	}

} # class
