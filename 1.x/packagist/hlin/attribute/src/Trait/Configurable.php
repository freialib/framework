<?php namespace hlin\attribute;

/**
 * @copyright (c) 2014, freia Team
 * @license BSD-2 <http://freialib.github.io/license.txt>
 * @package freia Library
 */
trait ConfigurableTrait {

	/**
	 * @var \hlin\archetype\Configs
	 */
	protected $confs = null;

	/**
	 * @codeCoverageIgnore
	 * @return static $this
	 */
	function confs_are(\hlin\archetype\Configs $confs) {
		$this->confs = $confs;
		return $this;
	}

} # trait
