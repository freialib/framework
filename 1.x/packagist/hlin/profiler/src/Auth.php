<?php namespace hlin\profiler;

/**
 * This overwrite of hlin.Auth adds access resolution logging. If a logger is
 * not registered with the Authorizer object this overwrite does nothing.
 *
 * @copyright (c) 2014, freia Team
 * @license BSD-2 <http://freialib.github.io/license.txt>
 * @package freia Library
 */
class Auth extends next\hlin\Auth {

	/**
	 * @var string uniq id used to distinguish between requests
	 */
	protected static $profile_id = null;

	/**
	 * @return boolean
	 */
	function can($entity, array $context = null, $attribute = null, $user_role = null) {
		$logger = $this->logger;
		$loggingtype = 'access';
		if ($this->logger != null) {
			$id = static::$profile_id;
			// is this the instance of a access request for this id?
			if ($id === null) {
				$id = static::$profile_id = base_convert(crc32(uniqid()), 10, 32);
				$logger->log("-- start of $id", $loggingtype, true);
			}

			$status = parent::can($entity, $context, $attribute, $user_role);

			// Convert context to human readable format
			// ----------------------------------------

			if ($context !== null) {
				$the_context = \hlin\Arr::join (
					', ', $context,
					function ($i, $v) {
						if ( ! is_array($v)) {
							if (is_string($v)) {
								return "$i => '$v'";
							}
							else if (is_null($v)) {
								return "$i -> NULL";
							}
							else if (is_object($v)) {
								return "$i -> Object(".get_class($v).')';
							}
							else { # something else
								return "$i => $v";
							}
						}
						else { # $v is an array
							// use of -> is intentional
							return "$i -> Array";
						}
					}
				);

				$the_context = "[ $the_context ]";
			}
			else { # context is null
				$the_context = '-';
			}

			$report = sprintf (
				"%-7s | %7s | %-12s | %-40s | %-60s || %s ",
				$id,
				$status ? 'ALLOWED' : 'DENIED',
				$user_role !== null ? $user_role : $this->role,
				$entity . ($attribute !== null ? ' -> '.$attribute : ''),
				$this->last_matched_type_code() === 0
					? 'no match'
					: $this->last_instigator().' via '.$this->last_matched_type().' on '.$this->last_matched_role(),
				$the_context
			);

			$logger->log($report, $loggingtype, true);

			return $status;
		}
		else { // no logging
			return parent::can($entity, $context, $attribute, $user_role);
		}
	}

} # class
