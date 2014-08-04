<?php namespace hlin\archetype;

/**
 * @copyright (c) 2014, freia Team
 * @license BSD-2 <http://freialib.github.io/license.txt>
 * @package freia Library
 */
trait LoggerTrait {

	/**
	 * Logs an exception.
	 */
	function logexception(\Exception $exception) {
		try {
			$message = $exception->getMessage();
			$trace = $exception->getTraceAsString();
			$trace = $this->becloud($trace);
			$trace = str_replace("\n", "\n\t", trim($trace, " \n\r\t"));
			$this->log("$message\n\n\t$trace\n");
		}
		catch (\Exception $e) {
			$this->failedLogging($e, $exception->getMessage());
		}
	}

	/**
	 * Save the data passed in as data.
	 */
	function var_dump($message, $data) {
		try {
			$export = trim(str_replace("\n", "\n\t", var_export($data, true)), "\n\t ");
			$this->log("$message\n\n\t$export\n");
		}
		catch (Exception $e) {
			$this->failedLogging($e, $message);
		}
	}

// ---- Private ---------------------------------------------------------------


	/**
	 * @var array
	 */
	protected $beclouding = null;

	/**
	 * @return static
	 */
	protected function beclouding_is($beclouding) {
		$this->beclouding = $beclouding;
		return $this;
	}

	/**
	 * This method is hook for implementation that can handle hiding, or
	 * need hiding.
	 *
	 * @return string beclouded version
	 */
	protected function becloud($clear) {
		if ($this->beclouding === null) {
			return $clear;
		}
		else { // path replacements
			$to_hide = [];
			foreach ($this->beclouding as $key => $str) {
				$to_hide[$str] = $key;
			}
			return strtr($clear, $to_hide);
		}
	}

	/**
	 * @param \Exception $e
	 */
	protected function failedLogging(\Exception $exception = null, $message = null) {
		try {

			if ($exception !== null) {
				$message = $exception->getMessage();
				$trace = $exception->getTraceAsString();
				$trace = str_replace("\n", "\n\t", "\n\t".trim($trace, " \n\r\t"));
				error_log("Error during logging process: $message\n$trace\n\n");
			}

			if ($message !== null) {
				error_log($message);
			}
			else if ($exception === null) {
				error_log("Logging failed for unknown reason.");
			}
		}
		catch (\Exception $e) {
			error_log($e->getMessage());
		}
	}

} # trait
