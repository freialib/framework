<?php namespace hlin\tools;

/**
 * @copyright (c) 2014, freia Team
 * @license BSD-2 <http://freialib.github.io/license.txt>
 * @package freia Library
 */
class Text {

	/**
	 * Uniforms indentation. Useful when printing.
	 *
	 * If you do not wish tabs to be converted to spaces set the value of
	 * $tabs_to_spaces to null.
	 *
	 * @return string
	 */
	static function reindent($source, $indent = '', $tabs_to_spaces = 4, $ignore_zero_indent = true) {
		// unify tabs
		if ($tabs_to_spaces !== null) {
			$source = str_replace("\t", str_repeat(' ', $tabs_to_spaces), $source);
		}

		// split into lines
		$lines = explode("\n", $source);

		// detect indent level
		$min_length = null;
		foreach ($lines as $line) {
			if (preg_match('#^([ \t]+)([^ ])#', $line, $matches)) {
				if ($min_length === null) {
					$min_length = strlen($matches[1]);
				}
				else if (strlen($matches[1]) < $min_length) {
					$min_length = strlen($matches[1]);
				}
			}
			else if ( ! $ignore_zero_indent) {
				$min_length = 0;
				break;
			}
		}

		// unify
		foreach ($lines as &$line) {
			if (preg_match('#^[ \t].*#', $line)) {
				$line = $indent.substr($line, $min_length);
			}
			else { # zero line
				$line = $indent.$line;
			}
		}

		return implode("\n", $lines);
	}

} # class
