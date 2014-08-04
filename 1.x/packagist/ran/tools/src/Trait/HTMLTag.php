<?php namespace ran\tools;

/**
 * @copyright (c) 2014, freia Team
 * @license BSD-2 <http://freialib.github.io/license.txt>
 * @package freia Library
 */
trait HTMLTagTrait {

	use \ran\MetaTrait;
	use \ran\RenderableTrait;

	/**
	 * @var string
	 */
	protected $tagname = null;

	/**
	 * @var mixed string or renderable
	 */
	protected $tagbody = null;

	/**
	 * @return string
	 */
	function tagname() {
		return $this->tagname;
	}

	/**
	 * @return static $this
	 */
	function tagname_is($tagname) {
		$this->tagname = $tagname;
		return $this;
	}

	/**
	 * @return static $this
	 */
	function tagbody_is($string) {
		$this->tagbody = $string;
		return $this;
	}

	/**
	 * @return static $this
	 */
	function tagbody_render($renderable) {
		$this->tagbody = $renderable;
		return $this;
	}

	/**
	 * If tag body is currently a non array value it will be converted to an
	 * array maintaining the previous body (along with the new one).
	 *
	 * @return static $this
	 */
	function appendtagbody($tagbody) {
		if (isset($this->tagbody) && ! is_array($this->tagbody)) {
			$this->tagbody = [ $this->tagbody ];
		}

		$this->tagbody[] = $tagbody;

		return $this;
	}

	/**
	 * @return mixed string or renderable
	 */
	function tagbody() {
		return $this->tagbody;
	}

	/**
	 * This class is an exception to the no __toString rule of Renderable. Using
	 * __toString in a case where another Renderable has been injected into
	 * the current Renderable object is still considered a grave mistake and
	 * implementations should issue an error in development; if feasible.
	 *
	 * @return string
	 */
	function __toString() {
		try {
			return $this->render();
		}
		catch (\Exception $exception) {
			return ''; # gracefully emmit the error
		}
	}

} # trait
