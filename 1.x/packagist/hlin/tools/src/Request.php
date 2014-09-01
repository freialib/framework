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
	 * Given a set of acceptable formats the system tries to retrieve the data
	 * format specified and return it in the specified output format. If there
	 * is no input the method will return null.
	 *
	 * Supported formats:
	 *  - post
	 *  - json
	 *  - query (alias: get)
	 *  - files
	 *
	 * Supported output formats:
	 *  - array
	 *
	 * @return mixed|null input
	 */
	function input(array $formats, $output = 'array') {

		if (in_array('post', $formats)) {
			$post = $this->context->web->requestPostData();
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

		if (in_array('query', $formats) || in_array('get', $formats)) {
			$get = $this->context->web->requestQueryData();
			if ( ! empty($get)) {
				if ($output == 'array') {
					return $get;
				}
			}
		}

		if (in_array('files', $formats)) {
			$files = $this->context->web->requestFiles();
			if ( ! empty($files)) {
				if ($output == 'array') {
					return $files;
				}
			}
		}

		// failed to resolve input
		return null;
	}

} # class
