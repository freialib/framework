<?php namespace hlin\tools;

/**
 * @copyright (c) 2014, freia Team
 * @license BSD-2 <http://freialib.github.io/license.txt>
 * @package freia Library
 */
class HelpCommand implements \hlin\archetype\Command {

	use \hlin\CommandTrait;

	/**
	 * @return int
	 */
	function main(array $args = null) {

		$cli = $this->cli;

		if ( ! empty($args)) {
			$targetgroup = array_shift($args);
		}
		else { // no args
			$targetgroup = null;
		}

		$version = \hlin\VersionCommand::instance($this->context);
		$version->main();
		$cli->printf("\n");

		// print header
		$this->delimiter();
		$cli->printf(" USAGE: %s command [ command specific behavior ]\n", $cli->syscall());
		$cli->printf("    eg. %s help\n", $cli->syscall());
		$cli->printf("    eg. %s help application\n", $cli->syscall());
		$cli->printf("    eg. %s ? example:command\n", $cli->syscall());

		$cli->printf("\n");

		$commands = $this->confs->read('freia/commands');

		// group tasks
		$topics = [];
		foreach ($commands as $name => $command) {
			if (isset($command['topic'])) {
				$topic = $command['topic'];
			}
			else { // unsorted tasks
				$topic = '-unsorted-';
			}

			isset($topics[$topic])
				or $topics[$topic] = [];

			$topics[$topic][$name] = $command;
		}

		// sort topics
		ksort($topics);

		// make sure application is first topic
		if ( isset($topics['application'])) {
			$applicationtopic = $topics['application'];
			unset($topics['application']);
			$topics = [ 'application' => $applicationtopic ] + $topics;
		}

		// make sure -unsorted- is last
		if ( isset($topics['-unsorted-'])) {
			$unsortedtopic = $topics['-unsorted-'];
			unset($topics['-unsorted-']);
			$topics = $topics + [ '-unsorted-' => $unsortedtopic ];
		}

		$topicformat = " %s\n";
		$commandformat  = "   %-15s - %s\n";
		foreach ($topics as $topic => $topic_commands) {
			if ($targetgroup !== null && $targetgroup !== $topic) {
				continue;
			}

			if ('application' !== $topic) {
				$cli->printf($topicformat, $topic);
			}

			foreach ($topic_commands as $name => $command) {

				if (isset($command['summary'])) {
					$summary = $command['summary'];
				}
				else { // no summary
					$summary = 'no summary available';
				}

				$cli->printf($commandformat, $name, $summary);
			}
			$cli->printf("\n");
		}
	}

// ---- Private ---------------------------------------------------------------

	/**
	 * ...
	 */
	function delimiter() {
		$this->cli->printf(str_repeat('-', 79)."\n");
	}

} # class
