<?php namespace hlin\tools;

/**
 * @copyright (c) 2014, freia Team
 * @license BSD-2 <http://freialib.github.io/license.txt>
 * @package freia Library
 */
trait ResponseTrait {

	use \hlin\ContextualTrait;

// ---- Response Logic --------------------------------------------------------

	#
	# Response logic is a function that recieves the output (typically from a
	# controller) and a configuration object set via the conf method on the
	# response.
	#

	/**
	 * @var callable
	 */
	protected $logic = null;

	/**
	 * Set the rendering logic to use in the final phase of processing.
	 *
	 * @return static $this
	 */
	function logic(callable $func) {
		$this->logic = $func;
		return $this;
	}

	/**
	 * @var mixed
	 */
	protected $conf = null;

	/**
	 * @return static $this
	 */
	function conf($conf) {
		$this->conf = $conf;
		return $this;
	}

	/**
	 * @return string
	 */
	function parse($input) {
		if ($this->logic !== null) {
			$func = $this->logic;
			return $func($input, $this->conf);
		}
		else { // logic === null
			return $input;
		}
	}

} # trait
