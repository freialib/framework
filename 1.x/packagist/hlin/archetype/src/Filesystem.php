<?php namespace hlin\archetype;

/**
 * This interface intentionally does not include ALL the php filesystem
 * functions, as seen here: http://www.php.net/manual/en/ref.filesystem.php
 *
 * If you really really need some of the other ones just Extend and Conquer.
 *
 * Note: out of the the functions included if there was a $context parameter
 * then said parameter is ommited here.
 *
 * @copyright (c) 2014, freia Team
 * @license BSD-2 <http://freialib.github.io/license.txt>
 * @package freia Library
 */
interface Filesystem {

	/**
	 * @see http://www.php.net/manual/en/function.file-exists.php
	 * @return boolean
	 */
	function file_exists($filename);

	/**
	 * @see http://www.php.net/manual/en/function.unlink.php
	 * @return boolean
	 */
	function unlink($filename);

	/**
	 * @see http://www.php.net/manual/en/function.chmod.php
	 * @return boolean
	 */
	function chmod($filename, $mode);

	/**
	 * @see http://www.php.net/manual/en/function.copy.php
	 * @return boolean
	 */
	function copy($source, $dest);

	/**
	 * @see http://www.php.net/manual/en/function.dirname.php
	 * @return string
	 */
	function dirname($path);

	/**
	 * @see http://www.php.net/manual/en/function.file-get-contents.php
	 * @return string
	 */
	function file_get_contents($filename);

	/**
	 * @see http://www.php.net/manual/en/function.file-put-contents.php
	 * @return boolean
	 */
	function file_put_contents($filename, $data);

	/**
	 * FILE_APPEND version of file_put_contents
	 * @see http://www.php.net/manual/en/function.file-put-contents.php
	 * @return boolean
	 */
	function file_append_contents($filename, $data);

	/**
	 * @see http://www.php.net/manual/en/function.filemtime.php
	 * @return int
	 */
	function filemtime($filename);

	/**
	 * @see http://www.php.net/manual/en/function.filesize.php
	 * @return int
	 */
	function filesize($filename);

	/**
	 * @see http://www.php.net/manual/en/function.filetype.php
	 * @return string fifo, char, dir, block, link, file, socket and unknown
	 */
	function filetype($filename);

	/**
	 * @see http://www.php.net/manual/en/function.is-dir.php
	 * @return boolean
	 */
	function is_dir($filename);

	/**
	 * @see http://www.php.net/manual/en/function.is-readable.php
	 * @return boolean
	 */
	function is_readable($filename);

	/**
	 * @see http://www.php.net/manual/en/function.is-writable.php
	 * @return boolean
	 */
	function is_writable($filename);

	/**
	 * @see http://www.php.net/manual/en/function.is-file.php
	 * @return boolean
	 */
	function is_file($filename);

	/**
	 * @see http://www.php.net/manual/en/function.mkdir.php
	 * @return boolean
	 */
	function mkdir($filename, $mode, $recursive = true);

	/**
	 * @see http://www.php.net/manual/en/function.realpath.php
	 * @return string
	 */
	function realpath($path);

	/**
	 * @see http://www.php.net/manual/en/function.rename.php
	 * @return boolean
	 */
	function rename($oldname, $newname);

	/**
	 * @see http://www.php.net/manual/en/function.rmdir.php
	 * @return boolean
	 */
	function rmdir($dirname);

	/**
	 * @see http://www.php.net/manual/en/function.touch.php
	 * @return boolean
	 */
	function touch($filename, $time = null);

	/**
	 * @see http://www.php.net/manual/en/function.scandir.php
	 * @return array
	 */
	function scandir($directory, $sorting_order = SCANDIR_SORT_ASCENDING);

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
	function glob($pattern, $flags = 0);

	/**
	 * @see http://www.php.net/manual/en/function.touch.php
	 * @return boolean
	 */
	function basename($path, $suffix = null);

	/**
	 * @codeCoverageIgnore
	 * @see http://www.php.net/manual/en/function.move-uploaded-file.php
	 * @return boolean
	 */
	function move_uploaded_file($filename, $destination);

} # interface
