<?php namespace hlin\archetype;

/**
 * @copyright (c) 2014, freia Team
 * @license BSD-2 <http://freialib.github.io/license.txt>
 * @package freia Library
 */
trait AuthorizerTrait {

// ---- Inspection ------------------------------------------------------------

	/**
	 * @var string last object to declare ban/pass
	 */
	protected $last_instigator = null;

	/**
	 * @var string role/alias by which the ban/pass was declared
	 */
	protected $last_matched_role = null;

	/**
	 * The match type of the last check
	 *  0 -- no explict ban or pass
	 *  1 -- direct pass (ie. protocol specific to user's role)
	 *  2 -- indirect pass (ie. related protocol)
	 *  3 -- ban (explicitly denied by protocol)
	 */
	protected $last_matched_type = 0;

	/**
	 * @return string
	 */
	function last_matched_role() {
		return $this->last_matched_role;
	}

	/**
	 * @return int
	 */
	function last_matched_type_code() {
		return $this->last_matched_type;
	}

	/**
	 * @return string
	 */
	function last_matched_type() {
		switch ($this->last_matched_type) {
			case 0: return 'no match';
			case 1:	return 'direct match';
			case 2:	return 'indirect match';
			case 3:	return 'explicit ban';
			default: throw new Panic('Logical error. Unknown last_matched_type.');
		}
	}

	/**
	 * @return string
	 */
	function last_instigator() {
		return $this->last_instigator;
	}

} # trait
