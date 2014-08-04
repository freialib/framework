<?php namespace ran\tools;

/**
 * @copyright (c) 2014, freia Team
 * @license BSD-2 <http://freialib.github.io/license.txt>
 * @package freia Library
 */
class Field extends \ran\HTMLTag {

	use \ran\FieldTrait;

	/**
	 * @return static
	 */
	static function instance(\hlin\archetype\Configs $confs) {
		$instance = parent::instance($confs);
		$instance->tagname_is('input');
		return $instance;
	}

	/**
	 * [!!] call autocompletefield before any other logic, when overwriting
	 *
	 * @return string
	 */
	function render() {

		$this->autocompletefield();
		$this->fieldtemplate !== null or $this->fieldtemplate = ':field';

		if ($this->fieldconfigurer_callback !== null) {
			call_user_func($this->fieldconfigurer_callback, $this);
		}

		return strtr($this->fieldtemplate, $this->fieldfillers());
	}

	/**
	 * Hook for adding additional fillers for hotwiring funcitonality.
	 *
	 * eg. say you need special classes on labels, you would overwrite this
	 * method to enable your templates to understand new inputs
	 *
	 * @return array
	 */
	protected function fieldfillers() {

		$fieldrender = $this->fieldrender();

		if ($this->hintrenderer !== null) {
			$callable = &$this->hintrenderer;
			$hintsrender = $callable($this->hints());
		}
		else { # no hint renderer
			$hintsrender = null;
		}

		if ($this->showerrors && $this->errorrenderer) {
			$callable = &$this->errorrenderer;
			$errorrrender = $callable($this->errors());
		}
		else { # no error renderer
			$errorrrender = null;
		}

		return array
			(
				':id'     => $this->get('id', null),
				':field'  => $fieldrender,
				':label'  => $this->fieldlabel(),
				':hints'  => $hintsrender,
				':errors' => $errorrrender,
			);
	}

	/**
	 * @return static $this
	 */
	function form_is($form) {
		$this->form = $form;
		$this->set('form', $form->signature());

		return $this;
	}

	/**
	 * @return static $this
	 */
	function form() {
		return $this->form;
	}

// ---- Private ---------------------------------------------------------------

	/**
	 * This helper will run once. Classes that overwrite render should call this
	 * method before performing calculations.
	 */
	protected function autocompletefield() {
		if ( ! $this->autocompleted) {
			$fieldname = $this->get('name', null);

			if ($fieldname !== null && ($autovalue = $this->form->autovalue($fieldname)) !== null) {
				$this->value_is($autovalue);
			}

			$this->autocompleted = true;
		}
	}

} # class
