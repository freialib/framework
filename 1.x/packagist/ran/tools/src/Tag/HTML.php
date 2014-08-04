<?php namespace ran\tools;

use \hlin\attribute\Configurable;

/**
 * @copyright (c) 2014, freia Team
 * @license BSD-2 <http://freialib.github.io/license.txt>
 * @package freia Library
 */
class HTMLTag implements Configurable {

	use \ran\HTMLTagTrait;
	use \hlin\ConfigurableTrait;

	/**
	 * @return static
	 */
	static function instance(\hlin\archetype\Configs $confs) {
		$i = new static;
		$i->confs_are($confs);
		$i->injectmetarenderers($confs->read('ran/htmltag')['metarenderers']);
		return $i;
	}

	/**
	 * @return \ran\HTMLTag
	 */
	static function i(\hlin\archetype\Configs $confs, $tagname, $tagbody = null) {
		$i = static::instance($confs);
		$i->tagname_is($tagname);
		$i->tagbody_is($tagbody);
		return $i;
	}

	/**
	 * @return string
	 */
	function render() {

		$tagname = $this->tagname();
		$tagname !== null or $tagname = 'span';

		$tagbody = $this->tagbody();

		if ( ! is_array($tagbody)) {
			$tagbody = [ $tagbody ];
		}

		$totalbody = null;
		foreach ($tagbody as $body) {
			if ($body !== null) {
				if (is_object($body)) {
					$totalbody .= $body->render();
				}
				else { # body is non-renderable
					$totalbody .= $body;
				}
			}
		}

		$tagattributes = $this->makeattributes();

		if ($totalbody === null) {
			return "<$tagname$tagattributes/>";
		}
		else { # body !== null
			return "<$tagname$tagattributes>$totalbody</$tagname>";
		}
	}

// ---- Private ---------------------------------------------------------------

	/**
	 * @return string
	 */
	protected function makeattributes() {

		$metadata = $this->metadata();
		$attributes = '';

		if ($metadata !== null) {
			foreach ($metadata as $key => $value) {
				if ($value === null) {
					continue;
				}

				$metarenderer = $this->metarenderer($key, null);

				if ($metarenderer !== null) {
					// false = don't render the attribute
					if ($metarenderer !== false) {
						$attributes .= ' '.$key.'="'.$metarenderer($this).'"';
					}
				}
				else if (is_array($value)) {
					$attributes .= ' '.$key.'="'.implode(' ', $value).'"';
				}
				else if ($value === '') {
					$attributes .= " $key";
				}
				else { # no meta renderer and value is not an array or empty
					$attributes .= " $key=\"$value\"";
				}
			}
		}

		return $attributes;
	}

} # class
