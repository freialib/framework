<?php namespace hlin\tools;

/**
 * @copyright (c) 2014, freia Team
 * @license BSD-2 <http://freialib.github.io/license.txt>
 * @package freia Library
 */
trait RequestTrait {

	use \hlin\ContextualTrait;

// ---- Parameters ------------------------------------------------------------

	/**
	 * @var array
	 */
	protected $params = [];

	/**
	 * @return static $this
	 */
	function params_are($params) {
		$this->params = $params;
		return $this;
	}

	/**
	 * @return mixed
	 */
	function param($name) {
		return isset($this->params[$name]) ? $this->params[$name] : null;
	}

	/**
	 * @return array
	 */
	function params() {
		return $this->params;
	}

} # trait
