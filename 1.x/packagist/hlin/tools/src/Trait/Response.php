<?php namespace hlin\tools;

/**
 * @copyright (c) 2014, freia Team
 * @license BSD-2 <http://freialib.github.io/license.txt>
 * @package freia Library
 */
trait ResponseTrait {

	use \hlin\ContextualTrait;

// ---- HTTP Headers ----------------------------------------------------------

	/**
	 * @var int
	 */
	protected $http_response_code = 200;

	/**
	 * Equivalent in functionality to http_response_code from PHP
	 *
	 * @see http://www.php.net/manual/en/function.http-response-code.php
	 * @return static $this
	 */
	function responseCode($code = null) {
		if ($code === null) {
			return $this->http_response_code;
		}
		else { // $code !== null, act as setter
			$this->http_response_code = $code;
			return $this;
		}
	}

	/**
	 * @var array
	 */
	protected $headers = [];

	/**
	 * @see http://www.php.net//manual/en/function.header.php
	 */
	function header($header, $replace = true, $http_response_code = null) {
		$this->headers[] = [$header, $replace, $http_response_code];
		return $this;
	}

	/**
	 * @return array
	 */
	function headers() {
		return $this->headers;
	}

	/**
	 * Redirect type can be 303 (see other), 301 (permament), 307 (temporary)
	 */
	function redirect($url, $type = 303) {
		if ($type == 303) {
			// 303 See Other
			$this->responseCode(303);
		}
		else if ($type == 301) {
			// 301 Moved Permanently
			$this->responseCode(301);
		}
		else if ($type == 307) {
			// 307 Temporary Redirect
			$this->responseCode(307);
		}

		// redirect to...
		$this->header("Location: $url");
	}

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
