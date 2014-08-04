<?php namespace hlin\tools;

/**
 * @copyright (c) 2014, freia Team
 * @license BSD-2 <http://freialib.github.io/license.txt>
 * @package freia Library
 */
class Request implements \hlin\tools\RequestSignature {

	use \hlin\RequestTrait;

	/**
	 * @return string
	 */
	function requestUri() {
		return $this->context->web->requestUri();
	}

	/**
	 * @return string
	 */
	function requestMethod() {
		return $this->context->web->requestMethod();
	}

	/**
	 * Given a set of acceptable formats the system tries to retrieve the
	 * data format specified and return it in the specified output format.
	 *
	 * Supported formats:
	 *  - post
	 *  - json
	 *
	 * Supported output formats:
	 *  - array
	 *
	 * @return mixed input
	 */
	function input(array $formats, $output = 'array') {

		if (in_array('post', $formats)) {
			$post = $this->context->web->postData();
			if ( ! empty($post)) {
				if ($output == 'array') {
					return $post;
				}
			}
		}

		if (in_array('json', $formats)) {
			$input = $this->context->web->requestBody();
			if ( ! empty($input)) {
				if ($output == 'array') {
					return json_decode($input, true);
				}
			}
		}

		throw new Panic('Failed to resolve input.');
	}

} # class
