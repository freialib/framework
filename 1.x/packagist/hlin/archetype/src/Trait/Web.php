<?php namespace hlin\archetype;

/**
 * @copyright (c) 2014, freia Team
 * @license BSD-2 <http://freialib.github.io/license.txt>
 * @package freia Library
 */
trait WebTrait {

	/**
	 * Redirect type can be 303 (see other), 301 (permament), 307 (temporary)
	 *
	 * [!!] This method will not exit the execution itself
	 */
	function redirect($url, $type = 303) {
		if ($type === 303) {
			$this->http_response_code(303);
		}
		else if ($type == 301) {
			$this->http_response_code(301);
		}
		else if ($type == 307) {
			$this->http_response_code(307);
		}

		// redirect to...
		$this->header("Location: $url");
	}

} # trait
