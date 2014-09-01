<?php namespace hlin\archetype;

/**
 * @copyright (c) 2014, freia Team
 * @license BSD-2 <http://freialib.github.io/license.txt>
 * @package freia Library
 */
class FilesystemMock implements \hlin\archetype\Filesystem {

	use \hlin\FilesystemTrait;

	public $mock_user    = null;
	public $mock_group   = null;
	public $mock_homedir = null;
	public $mock_pwd     = null;

	public $mock_filesystem = [
		'localdomain' => [
			'home' => [],
			'temp' => [],
			'var' => ['www' => []],
			'dev' => ['null' => null]
		]
	];

	/**
	 * ...
	 */
	static function instance($user = 'appuser', $usergroup = 'appgroup', $pwd = '~') {

		$i = new static;

		$i->mock_user = $user;
		$i->mock_group = $usergroup;
		$i->mock_homedir = $homedir = "/home/$user";
		$i->mock_pwd = str_replace('~', $homedir, $pwd);

		// mount localhost domain
		$vfs = & $i->mock_filesystem;
		$vfs['localhost'] = & $vfs['localdomain']['var']['www'];

		return $i;
	}

	/**
	 * Mount virtual filesystem at specified location.
	 *
	 * @return array path reference
	 */
	function & mock_mount(array $fs, $mountpoint = '~/approot') {
		$mountpoint = str_replace('~', $this->mock_homedir, trim($mountpoint, '/'));
		$normalizedfs = $this->mock_normalize($fs);
		$path = & $this->mock_ensurepath($mountpoint);

		$this->mock_fsmerge($path, $normalizedfs);

		return $path;
	}

	/**
	 * Merges two file system definitions togheter.
	 */
	function mock_fsmerge( & $fs, & $comp) {
		if (isset($comp['%type']) && $comp['%type'] == 'file') {
			throw new \Exception('[Mock] Tried to copy file as directory.');
		}

		// map existing files
		$existing_files = [];
		foreach ($fs as $node => $file) {
			if (is_integer($node)) {
				$existing_files[] = $file['filename'];
			}
		}

		// merge
		foreach ($comp as $node => $file) {
			if (is_integer($node)) {
				if (in_array($file['filename'], $existing_files)) {
					throw new \Exception("[Mock] Tried to overwrite file: {$file['filename']}");
				}
				$fs[] = $file;
			}
			else if ($node[0] == '%') { // attribute
				$fs[$node] = $file;
			}
			else { // dir
				isset($fs[$node]) or $fs[$node] = [];
				$this->mock_fsmerge($fs[$node], $file);
			}
		}
	}

	/**
	 * @return array
	 */
	function mock_normalize($fs) {
		if (isset($fs['%type']) && $fs['%type'] != 'dir') {
			return $fs;
		}

		$nfs = []; // normalized filesystem
		foreach ($fs as $node => $file) {
			if (is_integer($node)) {
				$nfs[] = $file;
			}
			else if ($node[0] == '%') { // attribute
				$nfs[$node] = $file;
			}
			else { // dir
				// calculate path
				if ($this->mock_is_invalidpath($node)) {
					throw new \Exception("[Mock] Invalid path: $node");
				}
				$pathparts = explode('/', $node);
				$partialfs = & $nfs;
				while (count($pathparts) > 0) {
					$pathpart = array_shift($pathparts);
					if (isset($partialfs[$pathpart])) {
						$partialfs = & $partialfs[$pathpart];
					}
					else { // path doesn't already exist
						// check a file doesn't exist with the same name
						foreach ($partialfs as $n => $f) {
							if (is_integer($n) && $f['filename'] == $pathpart) {
								throw new \Exception("[Mock] Unable to create path, a file with same name as required dir already exists: $pathpart");
							}
						}
						$partialfs[$pathpart] = [];
						$partialfs = & $partialfs[$pathpart];
					}
				}

				$normalizedfs = $this->mock_normalize($file);
				$this->mock_fsmerge($partialfs, $normalizedfs);
			}
		}

		return $nfs;
	}

	/**
	 * @return array dir
	 */
	function & mock_ensurepath($path) {
		$vfs = & $this->mock_filesystem;
		$path = str_replace('~', $this->mock_homedir, trim($path, '/'));

		if ($this->mock_is_invalidpath($path)) {
			throw new \Exception("[Mock] Invalid path: $path");
		}

		$fs = null;
		$debugpath = '';
		// check domain position
		if (($dp = stripos($path, ':')) !== false) {
			$domain = substr($path, 0, $dp);
			$path = trim(substr($path, $dp+1), '/');
			$debugpath .= "$domain:";
			if (isset($vfs[$domain])) {
				$fs = & $vfs[$domain];
			}
			else { // new domain
				$vfs[$domain] = [];
				$fs = & $vfs[$domain];
			}
		}
		else { // no : in path
			$fs = & $vfs['localdomain'];
			$debugpath .= 'localdomain:';
		}

		if ( ! empty($path)) {
			$pathparts = explode('/', $path);
			$partialpath = & $fs;
			while (count($pathparts) > 0) {
				$pathpart = array_shift($pathparts);
				$debugpath .= "/$pathpart";
				if (isset($partialpath[$pathpart])) {
					$partialpath = & $partialpath[$pathpart];
				}
				else { // path doesn't already exist
					// check a file doesn't exist with the same name
					foreach ($partialpath as $n => $f) {
						if (is_integer($n) && $f['filename'] == $pathpart) {
							throw new \Exception("[Mock] Unable to create path, a file with same name as required dir already exists: $debugpath");
						}
					}
					$partialpath[$pathpart] = [];
					$partialpath = & $partialpath[$pathpart];
				}
			}

			return $partialpath;
		}
		else { // path is domain root
			return $fs;
		}
	}

	/**
	 * @return array
	 */
	function mock_file($filename, $contents = '', $mode = 0777, $user = null, $group = null, $type = 'file', $time = 0) {
		return [
			'filename' => $filename,
			'contents' => $contents,
			'%type' => $type,
			'%time' => $time,
			'%mode' => $mode,
			'%user' => $user == null ? $this->mock_user : $user,
			'%group' => $group == null ? $this->mock_group : $group,
		];
	}

	/**
	 * @return boolean
	 */
	function mock_is_invalidpath($path) {
		return strpos($path, '%') !== false
			|| strpos($path, '*') !== false
			|| strpos($path, '?') !== false
			|| strpos($path, '|') !== false
			|| strpos($path, '"') !== false
			|| strpos($path, "'") !== false
			|| strpos($path, '<') !== false
			|| strpos($path, '>') !== false;
	}

	/**
	 * @return array|boolean|null null, dir, file or false if not found
	 */
	function & mock_get($path) {

		$vfs = & $this->mock_filesystem;
		$path = str_replace('~', $this->mock_homedir, trim($path, '/'));

		if ($this->mock_is_invalidpath($path)) {
			throw new \Exception("[Mock] Invalid path: $path");
		}

		$fs = null;
		// check domain position
		if (($dp = stripos($path, ':')) !== false) {
			$domain = substr($path, 0, $dp);
			$path = trim(substr($path, $dp+1), '/');
			if (isset($vfs[$domain])) {
				$fs = & $vfs[$domain];
				$debugpath = "$domain:";
			}
			else { // unknown domain
				throw new \Exception("[Mock] Unknown domain: $domain");
			}
		}
		else { // no :// in path
			$fs = & $vfs['localdomain'];
			$debugpath = "localdomain:";
		}

		// traverse
		$parts = explode('/', trim($path, '/'));
		if (count($parts) == 0) {
			return $fs;
		}
		else { // got segments
			$cwd = & $fs;
			while (count($parts) > 1) {
				$dir = array_shift($parts);
				$debugpath .= "/$dir";

				if ( ! isset($cwd[$dir])) {
					// is there a file with the same name?
					foreach ($cwd as $n => $def) {
						if (is_integer($n)) {
							if ($def['filename'] == $dir) {
								throw new Panic("[Mock] Unable to treat file as directory: $debugpath");
							}
						}
					}
					// there isn't a file with the name; path just doesn't exist
					$notfound = false;
					return $notfound;
				}

				$cwd = & $cwd[$dir];

				if ($cwd === null) {
					return null;
				}
			}

			$file = array_shift($parts);
			// is this a directory?
			if (isset($cwd[$file])) {
				return [
					'contents' => & $cwd[$file],
					'filename' => $file,
					'type' => 'dir',
					'mode' => isset($cwd[$file]['%mode']) ? $cwd[$file]['%mode'] : 0777,
					'user' => isset($cwd[$file]['%user']) ? $cwd[$file]['%user'] : $this->mock_user,
					'group' => isset($cwd[$file]['%group']) ? $cwd[$file]['%group'] : $this->mock_group,
				];
			}
			else { // has to be file
				foreach ($cwd as $n => $f) {
					if (is_integer($n)) {
						if ($f['filename'] == $file) {
							return $cwd[$n];
						}
					}
				}

				// file not found
				$notfound = false;
				return $notfound;
			}
		}
	}

	// ---- Mock --------------------------------------------------------------

	/**
	 * @see http://www.php.net/manual/en/function.file-exists.php
	 * @return boolean
	 */
	function file_exists($filename) {
		if ($this->mock_get($filename) !== false) {
			return true;
		}
		else { // file not found
			return false;
		}
	}

	/**
	 * @see http://www.php.net/manual/en/function.unlink.php
	 * @return boolean
	 */
	function unlink($filename) {
		throw new Panic('Missing Implementation');
	}

	/**
	 * @see http://www.php.net/manual/en/function.chmod.php
	 * @return boolean
	 */
	function chmod($filename, $mode) {
		throw new Panic('Missing Implementation');
	}

	/**
	 * @see http://www.php.net/manual/en/function.copy.php
	 * @return boolean
	 */
	function copy($source, $dest) {
		throw new Panic('Missing Implementation');
	}

	/**
	 * @see http://www.php.net/manual/en/function.dirname.php
	 * @return string
	 */
	function dirname($path) {
		throw new Panic('Missing Implementation');
	}

	/**
	 * @see http://www.php.net/manual/en/function.file-get-contents.php
	 * @return string
	 */
	function file_get_contents($filename) {
		throw new Panic('Missing Implementation');
	}

	/**
	 * @see http://www.php.net/manual/en/function.file-put-contents.php
	 * @return string
	 */
	function file_put_contents($filename, $data) {
		throw new Panic('Missing Implementation');
	}

	/**
	 * FILE_APPEND version of file_put_contents
	 * @see http://www.php.net/manual/en/function.file-put-contents.php
	 * @return boolean
	 */
	function file_append_contents($filename, $data) {
		throw new Panic('Missing Implementation');
	}

	/**
	 * @see http://www.php.net/manual/en/function.filemtime.php
	 * @return int
	 */
	function filemtime($filename) {
		throw new Panic('Missing Implementation');
	}

	/**
	 * @see http://www.php.net/manual/en/function.filesize.php
	 * @return int
	 */
	function filesize($filename) {
		throw new Panic('Missing Implementation');
	}

	/**
	 * @see http://www.php.net/manual/en/function.filetype.php
	 * @return string fifo, char, dir, block, link, file, socket and unknown
	 */
	function filetype($filename) {
		throw new Panic('Missing Implementation');
	}

	/**
	 * @see http://www.php.net/manual/en/function.is-dir.php
	 * @return boolean
	 */
	function is_dir($filename) {
		throw new Panic('Missing Implementation');
	}

	/**
	 * @see http://www.php.net/manual/en/function.is-readable.php
	 * @return boolean
	 */
	function is_readable($filename) {
		throw new Panic('Missing Implementation');
	}

	/**
	 * @see http://www.php.net/manual/en/function.is-writable.php
	 * @return boolean
	 */
	function is_writable($filename) {
		throw new Panic('Missing Implementation');
	}

	/**
	 * @see http://www.php.net/manual/en/function.is-file.php
	 * @return boolean
	 */
	function is_file($filename) {
		throw new Panic('Missing Implementation');
	}

	/**
	 * @see http://www.php.net/manual/en/function.mkdir.php
	 * @return boolean
	 */
	function mkdir($filename, $mode, $recursive = true) {
		throw new Panic('Missing Implementation');
	}

	/**
	 * @see http://www.php.net/manual/en/function.realpath.php
	 * @return string
	 */
	function realpath($path) {
		throw new Panic('Missing Implementation');
	}

	/**
	 * @see http://www.php.net/manual/en/function.rename.php
	 * @return boolean
	 */
	function rename($oldname, $newname) {
		throw new Panic('Missing Implementation');
	}

	/**
	 * @see http://www.php.net/manual/en/function.rmdir.php
	 * @return boolean
	 */
	function rmdir($dirname) {
		throw new Panic('Missing Implementation');
	}

	/**
	 * @see http://www.php.net/manual/en/function.touch.php
	 * @return boolean
	 */
	function touch($filename, $time = null) {
		throw new Panic('Missing Implementation');
	}

	/**
	 * @return array
	 */
	function scandir($directory, $sorting_order = SCANDIR_SORT_ASCENDING) {
		throw new Panic('Missing Implementation');
	}

	/**
	 * GLOB_MARK - Adds a slash to each directory returned
	 * GLOB_NOSORT - Return files as they appear in the directory (no sorting)
	 * GLOB_NOCHECK - Return the search pattern if no files matching it were found
	 * GLOB_NOESCAPE - Backslashes do not quote metacharacters
	 * GLOB_BRACE - Expands {a,b,c} to match 'a', 'b', or 'c'
	 * GLOB_ONLYDIR - Return only directory entries which match the pattern
	 * GLOB_ERR - Stop on read errors (like unreadable directories), by default errors are ignored.
	 *
	 * @codeCoverageIgnore
	 * @see http://www.php.net/manual/en/function.glob.php
	 * @return array
	 */
	function glob($pattern, $flags = 0) {
		throw new Panic('Missing Implementation');
	}

	/**
	 * @see http://www.php.net/manual/en/function.touch.php
	 * @return boolean
	 */
	function basename($path, $suffix = null) {
		throw new Panic('Missing Implementation');
	}

	/**
	 * @codeCoverageIgnore
	 * @see http://www.php.net/manual/en/function.move-uploaded-file.php
	 * @return boolean
	 */
	function move_uploaded_file($filename, $destination) {
		throw new Panic('Missing Implementation');
	}

} # mock
