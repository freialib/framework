<?php namespace hlin\tools;

/**
 * @copyright (c) 2014, freia Team
 * @license BSD-2 <http://freialib.github.io/license.txt>
 * @package freia Library
 */
class VersionCommand implements \hlin\archetype\Command {

	use \hlin\CommandTrait;

	/**
	 * @return int
	 */
	function main(array $args = null) {
		$cli = $this->cli;
		$cli->printf("\n %s %s\n", $this->context->mainversion(), $this->context->version());
		$subvers = $this->context->subversions();
		if ( ! empty($subvers)) {
			$info = [];
			foreach ($subvers as $name => $verinfo) {
				$info[] = "    $name {$verinfo['version']} by {$verinfo['author']}";
			}
			$cli->printf(implode("\n", $info)."\n");
		}
	}

} # class
