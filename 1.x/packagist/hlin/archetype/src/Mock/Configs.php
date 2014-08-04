<?php namespace hlin\archetype;

/**
 * @copyright (c) 2014, freia Team
 * @license BSD-2 <http://freialib.github.io/license.txt>
 * @package freia Library
 */
class ConfigsMock implements \hlin\archetype\Configs {

	use \hlin\ConfigsTrait;

	public $mock_confs = [];

	/**
	 * @return static
	 */
	static function instance($confs) {
		$i = new static;
		$i->confs = $confs;
		return $i;
	}

	// ---- Mocks -------------------------------------------------------------

	/**
	 * @return array
	 */
	function read($key) {
		if ( ! isset($this->mock_confs[$key])) {
			throw new Exception("[Mock] Unexpected configuration key: $key");
		}
		return $this->mock_confs[$key];
	}

} # mock
