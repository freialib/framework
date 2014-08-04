<?php namespace ran\tools;

/**
 * @copyright (c) 2014, freia Team
 * @license BSD-2 <http://freialib.github.io/license.txt>
 * @package freia Library
 */
class Form extends \ran\HTMLTag {

	use \ran\FormTrait;

	/**
	 * @var int
	 */
	protected static $formcounter = 1;

	/**
	 * @var int
	 */
	protected $fieldcounter = 1;

	/**
	 * @var array
	 */
	protected $autocomplete = null;

	/**
	 * @var int
	 */
	protected $formindex = null;

	/**
	 * @return static
	 */
	static function instance(\hlin\archetype\Configs $confs) {

		$i = parent::instance($confs);
		$i->tagname_is('form');
		$i->formindex = static::nextformindex();
		$i->set('id', $i->signature());

		// check if this form was previously submitted
		if (isset($_POST['form']) && $_POST['form'] === $i->signature()) {
			$i->autocomplete = &$_POST;
		}
		else { # POST not set, or fields not submitted for this form
			$i->autocomplete = null;
		}

		return $i;
	}

	/**
	 * @return static
	 */
	static function i(\hlin\archetype\Configs $confs, $standard, $action = null) {

		$action !== null or $action = ''; # default to current page

		$i = static::instance($confs);
		$i->set('action', $action);

		// apply standard
		if ($standard != null) {
			$i->apply($standard);
		}

		return $i;
	}

// ---- Primary Field Management ----------------------------------------------

	/**
	 * [!!] pseudofieldtype intentionally doesn't default to null
	 *
	 * [!!] pseudofieldtype is NOT fieldtype, if it is the identifier for what
	 * it is suppose to be
	 *
	 * @return \ran\Field
	 */
	function field($label, $fieldname, $fieldtype) {
		// field type loaders
		$fieldtypes = $this->confs->read('ran/htmlform')['fieldtypes'];

		if (isset($fieldtypes[$fieldtype])) {
			$instance = $fieldtypes[$fieldtype]($this, $this->confs);
		}
		else { # assume specialized class definition with no fieldtype loader
			$class = \hlin\PHP::pnn('ran.'.ucfirst($fieldtype).'Field');
			$instance = $class::instance($this->confs);
		}

		// configure and return
		return $instance
			->set('id', $this->signature($this->fieldcounter++))
			->set('name', $fieldname)
			->form_is($this)
			->fieldlabel_is($label)
			->fieldtemplate_is($this->fieldtemplate($fieldtype))
			->fieldconfigurer_is($this->fieldconfigurer($fieldtype))
			->hintrenderer_is($this->hintrenderer($fieldtype))
			->errorrenderer_is($this->errorrenderer($fieldtype))
			->adderrors($this->errors($fieldname));
	}

	/**
	 * Any additonal parameters are interpreted as Fields that are part of the
	 * composite. If an array is passed as second parameter the fields will be
	 * interpreted as text Field.
	 *
	 * Therefore the following:
	 *
	 *		$form->composite('Name', ['given_name', 'family_name']);
	 *
	 * Is equivalent to this:
	 *
	 *		$form->composite
	 *			(
	 *				'Name',
	 *				$form->text(null, 'given_name'),
	 *				$form->text(null, 'family_name')
	 *			);
	 *
	 * You may also specify a type by making entries associative:
	 *
	 *		[ 'address' => 'text', 'postalcode' => 'number' ]
	 *
	 * @return \ran\CompositeField
	 */
	function composite($label) {
		$args = func_get_args();
		array_shift($args); # remove $label

		$composite = \ran\CompositeField::instance($this->confs);
		$composite->fieldlabel_is($label);

		if (count($args) >= 1) {
			if (is_array($args[0])) {
				$array_shorthand = array_shift($args);
				foreach ($array_shorthand as $key => $value) {
					if (is_int($key)) {
						// treat value as fieldname
						$composite->addfield($this->field(null, $value, 'text'));
					}
					else { # key is not an int
						// treat key as fieldname and value as fieldtype
						$composite->addfield($this->field(null, $key, $value));
					}
				}
			}

			// add remaining HTMLFormFields
			foreach ($args as $field) {
				$composite->addfield($field);
			}
		}

		$composite
			->form_is($this)
			->fieldlabel_is($label)
			->fieldtemplate_is($this->fieldtemplate('composite'))
			->hintrenderer_is($this->hintrenderer('composite'))
			->errorrenderer_is($this->errorrenderer('composite'));

		return $composite;
	}

// ---- Errors & Values -------------------------------------------------------

	/**
	 * The given values will be used to autofill the form. They may be however
	 * ignored depending on context.
	 *
	 * @return static $this
	 */
	function autocomplete_array(array &$hints = null) {
		if ($this->autocomplete === null) {
			$this->autocomplete = &$hints;
		}
		else if ($hints !== null) {
			foreach ($hints as $key => $hint) {
				$this->autocomplete[$key] = $hint;
			}
		}

		return $this;
	}

	/**
	 * Retrieve autocomplete value for given field or null.
	 *
	 * @return mixed or null
	 */
	function autovalue($fieldname, $default = null) {
		if ($this->autocomplete !== null) {
			$fieldname = rtrim($fieldname, '[]');
			if (isset($this->autocomplete[$fieldname])) {
				return $this->autocomplete[$fieldname];
			}
		}

		// no auto complete value found
		return $default;
	}

// ---- Utility ---------------------------------------------------------------

	/**
	 * Returns the form signature or creates signature using given id and form
	 * signature.
	 *
	 * @return string
	 */
	function signature($id = null) {
		if ( ! $this->unsigned) {
			if ($this->get('id', null) === null) {
				$formsignature = 'freiaform'.$this->formindex;
			}
			else { # custom signature
				$formsignature = $this->get('id');
			}

			if ($id !== null) {
				return "{$formsignature}_field$id";
			}
			else { # form signature
				return $formsignature;
			}
		}
		else { # unsigned
			return null;
		}
	}

// ---- Autoconfiguration -----------------------------------------------------

	/**
	 * @return static $this
	 */
	function basicuploader() {
		$this->set('enctype', 'multipart/form-data');
		return $this;
	}

	/**
	 * @return static $this
	 */
	function nonuploader() {
		$this->set('enctype', null);
		return $this;
	}

// ---- Rendering -------------------------------------------------------------

	/**
	 * A "form" hidden field will be inserted into the form to identify the
	 * data submitted belonged to this form.
	 *
	 * @return string
	 */
	function render() {
		if ($this->show_metainfo) {
			$this->appendtagbody($this->hidden('form')
				->value_is($this->signature()));
		}

		return parent::render();
	}

// ---- Helpers ---------------------------------------------------------------

	/**
	 * @return string
	 */
	static function nextformindex() {
		return static::$formcounter++;
	}

} # class
