<?php namespace hlin\archetype;

/**
 * @copyright (c) 2014, freia Team
 * @license BSD-2 <http://freialib.github.io/license.txt>
 * @package freia Library
 */
trait ContextTrait {

	/**
	 * @var Logger
	 */
	public $logger = null;

	/**
	 * @return static $this
	 */
	function logger_is(Logger $logger) {
		$this->logger = $logger;
		return $this;
	}

// ---- Web Handlers ----------------------------------------------------------

	/**
	 * @var Web
	 */
	public $web = null;

	/**
	 * @return static $this
	 */
	function web_is(Web $web) {
		$this->web = $web;
		return $this;
	}

// ---- Configs Handlers ------------------------------------------------------

	/**
	 * @var Configs
	 */
	public $confs = null;

	/**
	 * @return static $this
	 */
	function confs_is(Configs $confs) {
		$this->confs = $confs;
		return $this;
	}

// ---- Filesystem Handlers ---------------------------------------------------

	/**
	 * @var Filesystem
	 */
	public $fs = null;

	/**
	 * @return static $this
	 */
	function filesystem_is(Filesystem $filesystem) {
		$this->fs = $filesystem;
		return $this;
	}

// ---- CLI Handlers ----------------------------------------------------------

	/**
	 * @var CLI
	 */
	public $cli = null;

	/**
	 * @return static $this
	 */
	function cli_is(CLI $cli) {
		$this->cli = $cli;
		return $this;
	}

// ---- Named Paths -----------------------------------------------------------

	/**
	 * @var array
	 */
	protected $registeredpaths = [];

	/**
	 * Registers a path under a specified name.
	 *
	 * If the path is already registered the method will Panic unless the 3rd
	 * parameter is passed.
	 */
	function addpath($name, $fspath, $overwrite = false) {
		if ( ! $overwrite && isset($this->registeredpaths[$name])) {
			throw new Panic("A path is already registered for $name.");
		}
		else { // path is not registered yet or overwrite == true
			$this->registeredpaths[$name] = $fspath;
		}
	}

	/**
	 * Retrieves the specified path. If the path is not registered the method
	 * will Panic, unless the second parameter is set, in which case it will
	 * just return null.
	 *
	 * @return string|null
	 */
	function path($name, $null_on_failure = false) {
		if ( ! isset($this->registeredpaths[$name])) {
			if ($null_on_failure) {
				return null;
			}
			else { // panic
				throw new Panic("There is no path registered for $name.");
			}
		}
		else { //
			return $this->registeredpaths[$name];
		}
	}

// ---- Special Options -------------------------------------------------------

	/**
	 * @var \hlin\archetype\Filemap
	 */
	protected $filemap = null;

	/**
	 * System file mapping.
	 */
	function filemap_is(\hlin\archetype\Filemap $filemap) {
		$this->filemap = $filemap;
	}

	/**
	 * @return \hlin\archetype\Filemap
	 */
	function filemap() {
		return $this->filemap;
	}

	/**
	 * @var \hlin\archetype\Autoloader
	 */
	protected $autoloader = null;

	/**
	 * System file mapping.
	 */
	function autoloader_is(\hlin\archetype\Autoloader $autoloader) {
		$this->autoloader = $autoloader;
	}

	/**
	 * @return \hlin\archetype\Autoloader
	 */
	function autoloader() {
		return $this->autoloader;
	}

// ---- Versions --------------------------------------------------------------

	/**
	 * @var string
	 */
	protected $mainversion = 'freia Library';

	/**
	 * @var array
	 */
	protected $versions = [
		'freia Library' => [ 'version' => '1.0.0', 'author' => 'freia Team' ],
	];

	/**
	 * @return string version
	 */
	function version() {
		return $this->versions[$this->mainversion]['version'];
	}

	/**
	 * @return array (version, author)
	 */
	function versioninfo() {
		return $this->versions[$this->mainversion];
	}

	/**
	 * @return array (version, author)
	 */
	function mainversion() {
		return $this->mainversion;
	}

	/**
	 * @return array
	 */
	function subversions() {
		$subversions = [];
		foreach ($this->versions as $key => $versioninfo) {
			if ($key !== $this->mainversion) {
				$subversions[$key] = $versioninfo;
			}
		}

		return $subversions;
	}

	/**
	 * @return static $this
	 */
	function addversion($name, $version, $author) {
		$this->versions[$name] = [ 'version' => $version, 'author' => $author ];
		return $this;
	}

	/**
	 * @return static $this
	 */
	function setmainversion($name) {
		$this->mainversion = $name;
		return $this;
	}

} # trait
