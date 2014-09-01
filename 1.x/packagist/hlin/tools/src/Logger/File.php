<?php namespace hlin\tools;

/**
 * @copyright (c) 2014, freia Team
 * @license BSD-2 <http://freialib.github.io/license.txt>
 * @package freia Library
 */
class FileLogger implements \hlin\archetype\Logger {

	use \hlin\LoggerTrait;

	/**
	 * @var string
	 */
	protected $logspath = null;

	/**
	 * @var \hlin\archetype\Filesystem
	 */
	protected $fs = null;

	/**
	 * @return static
	 */
	static function instance(\hlin\archetype\Filesystem $fs, $logspath, array $beclouding = null) {

		$logspath = realpath($logspath);

		# Logs are too easily "broken" by mishaps to crash the system when
		# they are incorrectly configured

		if ( ! file_exists($logspath)) {
			error_log("The log path {$logspath} does not exist");
			return \hlin\NoopLogger::instance();
		}

		if ( ! is_writable($logspath)) {
			error_log("The log path {$logspath} is not writable");
			return \hlin\NoopLogger::instance();
		}

		$i = new static;
		$i->beclouding_is($beclouding);
		$i->logspath = $logspath;
		$i->fs = $fs;
		return $i;
	}

	/**
	 * Logs a message. The type can be used as a hint to the logger on how to
	 * log the message. If the logger doesn't understand the type a file with
	 * the type name will be created as default behavior.
	 *
	 * Types should not use illegal file characters.
	 */
	function log($message, $type = null, $explicit = false) {
		try {
			$time = date('Y-m-d|H:i:s');
			if ($type === null) {
				$this->appendToFile(date('Y/m/d'), "[$time] $message", $explicit);
			}
			else { // type !== null
				$this->appendToFile($type, "[$time] $message", $explicit);
			}
		}
		catch (\Exception $e) {
			$this->failedLogging($e, $message);
		}
	}

// ---- Private ---------------------------------------------------------------

	/**
	 * @var array
	 */
	protected $filesigs = [];

	/**
	 * @var array
	 */
	protected $sigs = [];

	/**
	 * Appeds to the logfile. The logfile is relative to the logs directory.
	 * If the logfile doesn't exist yet the system will create it. The '.log'
	 * extention is automatically appended, along with a single
	 * newline character.
	 */
	protected function appendToFile($logfile, $logstring, $explicit = false) {

		$class = \hlin\PHP::unn(__CLASS__);

		# logging signatures are intended to prevent duplication PER REQUEST
		# on a per logger instance basis, not cull duplicate messages per log

		$sig = crc32($logstring);
		$filesig = crc32($sig.$logfile);

		// have we already logged the message?
		if ( ! in_array($filesig, $this->filesigs)) {

			$fs = $this->fs;
			$filepath = $this->logspath.'/'.$logfile.'.log';
			$dir = $fs->dirname($filepath);

			if ( ! $fs->file_exists($dir)) {
				if ( ! $fs->mkdir($dir, $this->dirPermission(), true)) {
					$this->failedLogging(null, "[$class] Failed to create directories: $dir");
					return;
				}
			}

			// does the file exist?
			if ( ! $fs->file_exists($filepath)) {
				// ensure the file exists
				if ( ! $fs->touch($filepath)) {
					$this->failedLogging(null, "[$class] Failed to create log file: $filepath");
					return;
				}
				// ensure the permissions are right
				if ( ! $fs->chmod($filepath, $this->filePermission())) {
					$this->failedLogging(null, "[$class] Failed to set permissions on log file: $filepath");
					return;
				}
			}

			if ( ! $fs->file_append_contents($filepath, "$logstring\n")) {
				$this->failedLogging(null, "[$class] Failed to append to log: $filepath");
			}

			// intentionally recording signature only after success
			$this->filesigs[] = $filesig;

			// have we stored the summary report?
			if ( ! $explicit && ! in_array($sig, $this->sigs)) {

				$filepath = $this->logspath.'/summary.log';

				# we don't need to ensure directory structure; it's already
				# been ensured by previous operations of recording the long
				# term complete log message

				// we only record the first line in the summary log
				if (($ptr = stripos($logstring, "\n")) !== false) {
					$summary = trim(substr($logstring, 0, $ptr));
				}
				else { // no end of line detected
					$summary = trim($logstring);
				}

				if ( ! $fs->file_append_contents($filepath, "$summary\n")) {
					$this->failedLogging(null, "[$class] Failed to append to log: $filepath");
				}

				// intentionally recording signature only after success
				$this->sigs[] = $sig;
			}
		}
	}

	/**
	 * Overwrite hook.
	 *
	 * @return int
	 */
	protected function dirPermission() {
		return 0775;
	}

	/**
	 * Overwrite hook.
	 *
	 * @return int
	 */
	protected function filePermission() {
		return 0664;
	}

} # class
