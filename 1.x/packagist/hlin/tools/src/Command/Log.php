<?php namespace hlin\tools;

/**
 * @copyright (c) 2014, freia Team
 * @license BSD-2 <http://freialib.github.io/license.txt>
 * @package freia Library
 */
class LogCommand implements \hlin\archetype\Command {

	use \hlin\CommandTrait;

	/**
	 * @return int
	 */
	function main(array $args = null) {
		$ctx = $this->context;
		$logspath = $ctx->path('logspath');

		if ($logspath === false) {
			throw new Panic('To use the log command your context must have the [logspath] path set.');
		}

		$summarylog = "$logspath/summary.log";
		if ($ctx->fs->file_exists($summarylog)) {
			$ctx->fs->file_put_contents($summarylog, '');
		}

		$ctx->cli->passthru("tail -f -n0 '$summarylog'");
	}

} # class
