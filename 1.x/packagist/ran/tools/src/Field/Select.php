<?php namespace ran\tools;

/**
 * @copyright (c) 2014, freia Team
 * @license BSD-2 <http://freialib.github.io/license.txt>
 * @package freia Library
 */
class SelectField extends \ran\Field {

	/**
	 * @var array
	 */
	protected $options = null;

	/**
	 * @var array
	 */
	protected $optgroups = null;

	/**
	 * @var array
	 */
	protected $logicaloptions = null;

	/**
	 * @var array
	 */
	protected $liefhierarchy = null;

	/**
	 * @return static
	 */
	static function instance(\hlin\archetype\Configs $confs) {
		$instance = parent::instance($confs);
		$instance->tagname_is('select');
		return $instance;
	}

	/**
	 * Inserts options via associtive array of key => value pairs.
	 *
	 * See optgroups_array for option group version.
	 *
	 * @return static $this
	 */
	function options_array(array $array = null) {
		$this->options = $array;
		return $this;
	}

	/**
	 * The hierarchy will be rendered by disabling entries that point to null.
	 * You may provide intending to the items and set categories to null to
	 * achieve a simple hierarchy system. The &mdash; is recomended for
	 * indentation.
	 *
	 * For pretty displaying it's recomended you parse the options and do a
	 * javascript replacement of the select field.
	 *
	 * @return static $this
	 */
	function options_logical(array $hierarchy = null) {
		$this->logicaloptions = $hierarchy;
		return $this;
	}

	/**
	 * In a lief hierarchy only the liefs are selectable. You specify the
	 * hierarchy though an array with inner arrays. If a key points to an
	 * arrays it is considered a group label, otherwise it's rendered as an
	 * option.
	 *
	 * The hierarchy will be rendered by disabling category labels so it
	 * appears consistent (unlike the empty optgroup method).
	 *
	 * The items are expected to be already indented.
	 * Using &mdash; is recomended for indenting leaf hierarchies.
	 *
	 * If you want all items to be selectable use options_logical.
	 *
	 * For pretty displaying it's recomended you parse the options and do a
	 * javascript replacement of the select field.
	 *
	 * @return static $this
	 */
	function options_liefhierarchy(array $hierarchy = null) {
		$this->liefhierarchy = $hierarchy;
		return $this;
	}

	/**
	 * Insert options via associative array of groups pointing to associative
	 * array of options. Note that optgroups are treated as seperate entities
	 * to options, so you can have both in the same select.
	 *
	 * If normal options are present, groups are rendered after them.
	 *
	 * @return static $this
	 */
	function optgroups_array(array $optgroups = null) {
		$this->optgroups = $optgroups;
		return $this;
	}

	/**
	 * @return string
	 */
	function fieldrender() {

		$this->autocompletefield();
		$this->tagbody_is('');

		if ($this->options !== null) {
			foreach ($this->options as $value => $label) {
				$option = \ran\HTMLTag::i($this->confs, 'option', $label)->set('value', $value);

				if ($this->values !== null && in_array(strval($value), $this->values, false)) {
					$option->set('selected', '');
				}

				$this->appendtagbody($option->render());
			}
		}

		if ($this->optgroups !== null) {
			foreach ($this->optgroups as $group => $options) {
				$optgroup = \ran\HTMLTag::i($this->confs, 'optgroup')->set('label', $group);
				foreach ($options as $value => $label) {
					$option = \ran\HTMLTag::i($this->confs, 'option', $label)->set('value', $value);
					if ($this->values !== null && \in_array(\strval($value), $this->values, false)) {
						$option->set('selected', '');
					}
					$optgroup->appendtagbody($option->render());
				}
				$this->appendtagbody($optgroup->render());
			}
		}

		if ($this->logicaloptions !== null) {
			foreach ($this->logicaloptions as $key => $label) {
				if ($label === null) {
					$option = \ran\HTMLTag::i($this->confs, 'option', $key)
						->set('disabled', '');
					$this->appendtagbody($option->render());
				}
				else { # $label !== null
					$option = \ran\HTMLTag::i($this->confs, 'option', $label)
						->set('value', $key);

					if ($this->values !== null && in_array(strval($key), $this->values, false)) {
						$option->set('selected', '');
					}

					$this->appendtagbody($option->render());
				}
			}
		}

		if ($this->liefhierarchy !== null) {
			$this->liefhierarchy_parse_options($this->liefhierarchy);
		}

		return parent::fieldrender();
	}

	/**
	 * Parses hierarchy input array.
	 */
	protected function liefhierarchy_parse_options(array $options) {
		foreach ($options as $key => $label) {
			if (is_array($label)) {
				$option = \ran\HTMLTag::i($this->confs, 'option', $key)
					->set('disabled', '');

				$this->appendtagbody($option->render());
				$this->liefhierarchy_parse_options($label);
			}
			else { # $value not array
				$option = \ran\HTMLTag::i($this->confs, 'option', $label)
					->set('value', $key);

				if ($this->values !== null && in_array(strval($key), $this->values, false)) {
					$option->set('selected', '');
				}

				$this->appendtagbody($option->render());
			}
		}
	}

	/**
	 * @var array selected values
	 */
	protected $values = null;

	/**
	 * Inserts values by interpreting tablular array as is typically the result
	 * of a SQL call. If the table is nonstandard you can provide an idkey and
	 * titlekey to help the function retrieve the correct values.
	 *
	 * Typically defaults for idkey and title key are "id" and "title."
	 *
	 * If groupkey is provided the method will insert optgroups instead of
	 * normal options, unless the value for the group is null.
	 *
	 * @return static $this
	 */
	function options_table(array $table, $valuekey = null, $labelkey = null, $groupkey = null) {
		$optgroups = [];
		$options = [];

		if ($groupkey === null) {
			$options = static::associativeFrom($table, $valuekey, $labelkey);
		}
		else { # got group key
			foreach ($table as $row) {
				if ($row[$groupkey] !== null) {
					isset($optgroups[$row[$groupkey]]) or $optgroups[$row[$groupkey]] = [];
					$optgroups[$row[$groupkey]][$row[$valuekey]] = $row[$labelkey];
				}
				else { # null group
					$options[$row[$valuekey]] = $row[$labelkey];
				}
			}
		}

		$this->options_array($options);
		$this->optgroups_array($optgroups);

		return $this;
	}

	/**
	 * @return static $this
	 */
	function value_is($value) {
		if (is_array($value)) {
			$this->value_array($value);
		}
		else { # single value
			$this->value_array([ $value ]);
		}
		return $this;
	}

	/**
	 * @return static $this
	 */
	function value_array(array $values = null) {
		// normalize values
		if ($values) {
			$this->values = null;
			foreach ($values as $value) {
				$this->values[] = $value;
			}
		}
		return $this;
	}

// ---- Private ---------------------------------------------------------------

	/**
	 * Given an array converts it to an associative array based on the key
	 * and value reference provided.
	 *
	 * @param array array
	 * @param string key reference
	 * @param string value reference
	 * @return array
	 */
	protected static function associativeFrom(array &$table, $key_ref = 'id', $value_ref = 'title') {
		$assoc = [];
		foreach ($table as $row) {
			$assoc[$row[$key_ref]] = $row[$value_ref];
		}
		return $assoc;
	}

} # class
