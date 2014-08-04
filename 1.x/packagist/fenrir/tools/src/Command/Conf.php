<?php namespace fenrir\tools;

/**
 * @copyright (c) 2014, freia Team
 * @license BSD-2 <http://freialib.github.io/license.txt>
 * @package freia Library
 */
class ConfCommand implements \hlin\archetype\Command {

	use \hlin\CommandTrait;

	/**
	 * @return int
	 */
	function main(array $args = null) {

		$cli = $this->cli;

		if (empty($args)) {
			$cli->printf("Incorrect command invokation.");
			return 500;
		}

		$command = array_shift($args);

		if ($command == 'scan') {
			return $this->scan();
		}
		else { // non-scan command
			if (empty($args)) {
				$cli->printf("Missing configuration path.");
				return 500;
			}

			$param = array_shift($args);

			if ($command == 'show') {
				$conf = $this->confs->read($param);
				$this->printconfig($conf);
				return 0;
			}
			else if ($command == 'all') {
				$filemap = $this->context->filemap();
				$files = $filemap->file($this->fs, "confs/$param.php", 'cfs-files');
				if ($files != null) {
					$rootpath = $this->context->path('rootpath');
					foreach ($files as $file) {
						$prettyfilename = str_replace(DIRECTORY_SEPARATOR, '/', str_replace($rootpath, 'rootpath:', $file));
						$cli->printf("\n  $prettyfilename\n ".str_repeat('-', 77)." \n\n");
						$conf = $this->_include($file);
						$this->printconfig($conf);
						$cli->printf("\n");
					}
					return 0;
				}
				else { // no files found
					$cli->printf("Could not find any configuration file.");
					return 404;
				}
			}
			else if ($command == 'files') {
				$filemap = $this->context->filemap();
				$files = $filemap->file($this->fs, "confs/$param.php", 'cfs-files');
				if ($files != null) {
					$rootpath = $this->context->path('rootpath');
					$idx = 1;
					$cli->printf("\n Lower index means higher priority (ie. top files overwrite lower).\n\n");
					foreach ($files as $file) {
						$prettyfilename = str_replace(DIRECTORY_SEPARATOR, '/', str_replace($rootpath, 'rootpath:', $file));
						$cli->printf("  %02s  $prettyfilename\n", $idx);
						$idx++;
					}
					return 0;
				}
				else { // no files found
					$cli->printf("Could not find any configuration file.");
					return 404;
				}
			}
			else { // unknown command
				$cli->printf("Unrecognized command.");
				return 500;
			}
		}

		return 0;
	}

// ---- Private ---------------------------------------------------------------

	/**
	 * @return mixed
	 */
	protected function _include($file) {
		return include $file;
	}

	/**
	 * ...
	 */
	protected function printconfig($conf) {
		$this->cli->printf(var_export($conf, true));
	}

	/**
	 * @return int
	 */
	protected function scan() {
		$fs = $this->fs;
		$cli = $this->cli;
		$paths = $this->context->autoloader()->paths();
		$confs = [];
		foreach ($paths as $path) {
			$confs = array_merge($confs, $this->cleanentries($fs->glob("$path/confs/*.php"), $path));
			$dirs = $fs->glob("$path/confs/*", GLOB_ONLYDIR);
			foreach ($dirs as $path) {
				$confs = array_merge($confs, $this->recursive_scan($fs->basename($path), $fs, $path));
			}
		}

		$confs = array_unique($confs);
		sort($confs);

		foreach ($confs as $conf) {
			$cli->printf("$conf\n");
		}

		return 0;
	}

	/**
	 * @return array
	 */
	protected function recursive_scan($prefix, \hlin\archetype\Filesystem $fs, $path) {
		$confs = $this->cleanentries($fs->glob("$path/*.php"), $path);
		$dirs = $fs->glob("$path/*", GLOB_ONLYDIR);
		foreach ($dirs as $p) {
			$base = $fs->basename($p);
			$confs = array_merge($confs, $this->recursive_scan($base, $fs, $p));
		}

		foreach ($confs as & $conf) {
			$conf = $prefix.'/'.$conf;
		}

		return $confs;
	}

	/**
	 * @return array
	 */
	protected function cleanentries(array $files, $path) {
		$confs = [];
		foreach ($files as $file) {
			$confs[] = preg_replace("/\.php$/", '', ltrim(str_replace($path, '', $file), '/\\'));
		}

		return $confs;
	}

} # class
