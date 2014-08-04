<?php namespace hlin\archetype;

/**
 * @copyright (c) 2014, freia Team
 * @license BSD-2 <http://freialib.github.io/license.txt>
 * @package freia Library
 */
class CLIMock implements \hlin\archetype\CLI {

	use \hlin\CLITrait;

	public $mock_syscall = '/mock/bin/syscall';
	public $mock_stdin = [];
	public $mock_stdout_calls = [];
	public $mock_stdout = '';
	public $mock_stderr_calls = [];
	public $mock_stderr = '';

	/**
	 * @return static
	 */
	static function instance($stdin, $args, $command, $flags) {
		$i = new static;
		$i->mock_stdin = $stdin;
		$i->args = $args;
		$i->command = $command;
		$i->flags = $flags;
		return $i;
	}

	// ---- Mocks -------------------------------------------------------------

	/**
	 * @codeCoverageIgnore
	 * @see http://php.net/manual/en/function.passthru.php
	 */
	function passthru($command, &$return_var = null) {
		throw new Panic('Missing Implementation');
	}

	/**
	 * @codeCoverageIgnore
	 * @see http://php.net/manual/en/function.system.php
	 * @return string
	 */
	function system($command, &$return_var = null) {
		throw new Panic('Missing Implementation');
	}

	/**
	 * @codeCoverageIgnore
	 * @see http://php.net/manual/en/function.exec.php
	 * @return string
	 */
	function exec($command, array &$output = null, &$return_var = null) {
		throw new Panic('Missing Implementation');
	}


	/**
	 * ...
	 */
	function fgets() {
		if (empty($this->mock_stdin)) {
			throw new \Exception('[Mock] Unexpected user input call.');
		}
		return array_pop($this->mock_stdin);
	}

	/**
	 * ...
	 */
	function printf($format) {
		array_push($this->mock_stdout_calls, func_get_args());
		$this->mock_stdout .= call_user_func_array(sprintf, func_get_args());
	}

	/**
	 * ...
	 */
	function printf_error($format) {
		array_push($this->mock_stderr_calls, func_get_args());
		$this->mock_stderr .= call_user_func_array(sprintf, func_get_args());
	}

} # mock
