<?php namespace hlin\tools;

/**
 * @copyright (c) 2014, freia Team
 * @license BSD-2 <http://freialib.github.io/license.txt>
 * @package freia Library
 */
class ModuleStackCommand implements \hlin\archetype\Command {

	use \hlin\CommandTrait;

	/**
	 * @return int
	 */
	function main(array $args = null) {
		$autoloader = $this->context->autoloader();
		foreach ($autoloader->paths() as $key => $path) {
			$this->cli->printf(" - $key\n");
		}
		$this->cli->printf("\n");
	}

} # class