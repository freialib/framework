<?php namespace ran\tools;

/**
 * @copyright (c) 2014, freia Team
 * @license BSD-2 <http://freialib.github.io/license.txt>
 * @package freia Library
 */
trait RenderableTrait {

	/**
	 * @var array of keyed callables
	 */
	protected $metarenderers = null;

	/**
	 * Registers a function that may be used in the rendering process. If such a
	 * function is used and how it is used is based on the implementation and
	 * context of the class.
	 *
	 * @return static $this
	 */
	function addmetarenderer($key, callable $metarenderer) {
		$this->metarenderers[$key] = $metarenderer;
		return $this;
	}

	/**
	 * Instructs the system not to render the key in question. Handlers will
	 * recieve false for the key.
	 *
	 * @return static $this
	 */
	function dontrender($key) {
		$this->metarenderers[$key] = false;
		return $this;
	}

	/**
	 * @return callable
	 */
	function metarenderer($key, $default = null) {
		if (isset($this->metarenderers, $this->metarenderers[$key])) {
			return $this->metarenderers[$key];
		}

		return $default;
	}

	/**
	 * See: addrenderhelper above
	 *
	 * Mass inject array of render helpers.
	 *
	 * @return static $this
	 */
	function injectmetarenderers(array $metarenderers = null) {
		if ($metarenderers === null) {
			return $this;
		}

		if ($this->metarenderers === null) {
			$this->metarenderers = $metarenderers;
		}
		else { # we have existing render helpers
			foreach ($metarenderers as $key => $helper) {
				$this->metarenderers[$key] = $helper;
			}
		}

		return $this;
	}

} # trait
