<?php namespace ran\tools;

/**
 * @copyright (c) 2014, freia Team
 * @license BSD-2 <http://freialib.github.io/license.txt>
 * @package freia Library
 */
trait MetaTrait {

	/**
	 * @var array
	 */
	protected $metadata = null;

	/**
	 * @return mixed
	 */
	function &get($name, $default = null) {
		if (isset($this->metadata[$name])) {
			return $this->metadata[$name];
		}
		else { # metadata for name is not set
			return $default;
		}
	}

	/**
	 * @return static $this
	 */
	function set($name, $value) {
		$this->metadata[$name] = $value;
		return $this;
	}

	/**
	 * If the key is currently a non-array value it will be converted to an
	 * array  maintaning the previous value (along with the new one).
	 *
	 * @return static $this
	 */
	function add($name, $value) {
		if (isset($this->metadata[$name]) && ! is_array($this->metadata[$name])) {
			$this->metadata[$name] = [ $this->metadata[$name] ];
		}

		$this->metadata[$name][] = $value;

		return $this;
	}

	/**
	 * Convenient way to add multiple meta attributes in a single call.
	 *
	 * Roughly equivalent to calling foreach on $values and performing add.
	 *
	 * @return static $this
	 */
	function addmeta($name, $values = null) {
		if ($values != null) {
			foreach ($values as $value) {
				$this->add($name, $value);
			}
		}

		return $this;
	}

	/**
	 * @return static $this
	 */
	function metadata_is(array $metadata = null) {
		$this->metadata = $metadata;
		return $this;
	}

	/**
	 * @return array
	 */
	function &metadata() {
		return $this->metadata;
	}

} # trait
