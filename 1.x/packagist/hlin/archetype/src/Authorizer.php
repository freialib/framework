<?php namespace hlin\archetype;

/**
 * @copyright (c) 2014, freia Team
 * @license BSD-2 <http://freialib.github.io/license.txt>
 * @package freia Library
 */
interface Authorizer {

	/**
	 * @return boolean
	 */
	function can($entity, array $context = null, $attribute = null, $user_role = null);

} # interface
