<?php namespace hlin\archetype;

/**
 * @copyright (c) 2014, freia Team
 * @license BSD-2 <http://freialib.github.io/license.txt>
 * @package freia Library
 */
class ContextMock implements \hlin\archetype\Context {

	use \hlin\ContextTrait;

	/**
	 * @return static
	 */
	static function instance($cli, $fs) {
		$i = new static;
		$i->cli_is($cli);
		$i->filesystem_is($fs);
		return $i;
	}

	/**
	 * @codeCoverageIgnore
	 * @see http://php.net/manual/en/function.php-sapi-name.php
	 * @return string
	 */
	function php_sapi_name() {
		throw new Panic('Missing Implementation');
	}

} # mock
