<?php namespace hlin\archetype;

/**
 * @copyright (c) 2014, freia Team
 * @license BSD-2 <http://freialib.github.io/license.txt>
 * @package freia Library
 */
trait CLITrait {

	/**
	 * @var int
	 */
	protected $echolevel = 0;

	/**
	 * @var string
	 */
	protected $syscall = null;

	/**
	 * @var string
	 */
	protected $command = null;

	/**
	 * @var array
	 */
	protected $flags = null;

	/**
	 * @codeCoverageIgnore
	 * @return array
	 */
	function args() {
		return $this->argv;
	}

	/**
	 * @codeCoverageIgnore
	 * @return string
	 */
	function syscall() {
		return $this->syscall;
	}

	/**
	 * @codeCoverageIgnore
	 * @return string
	 */
	function command() {
		return $this->command;
	}

	/**
	 * @codeCoverageIgnore
	 * @return array
	 */
	function flags() {
		return $this->flags;
	}

	/**
	 * ...
	 */
	function echoOff() {
		$this->echolevel++;
	}

	/**
	 * ...
	 */
	function echoOn() {
		$this->echolevel--;
		if ($this->echolevel < 0) {
			throw new Panic('Invalid echo level. Likely caused by misbehaving processes.');
		}
	}

	/**
	 * Shorthand for retrieving confirmation from console. Common console task
	 * for input.
	 *
	 * @return string answer
	 */
	function ask($question, array $answers) {
		// question loop
		$optstr = implode("|", $answers);
		do {
			$this->printf("$question [$optstr] ");
			$answer = $this->fgets();
		}
		while ( ! in_array($answer, $answers));

		return $answer;
	}

} # trait
