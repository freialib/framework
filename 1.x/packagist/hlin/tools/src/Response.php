<?php namespace hlin\tools;

/**
 * @copyright (c) 2014, freia Team
 * @license BSD-2 <http://freialib.github.io/license.txt>
 * @package freia Library
 */
class Response implements \hlin\tools\ResponseSignature {

	use \hlin\ResponseTrait;

// ---- HTTP Headers & Response Codes -----------------------------------------

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

} # class
