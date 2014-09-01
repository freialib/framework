<?php namespace hlin\profiler;

/**
 * @copyright (c) 2014, freia Team
 * @license BSD-2 <http://freialib.github.io/license.txt>
 * @package freia Library
 */
class AccessLogCommand implements \hlin\archetype\Command {

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

		$accesslog = "$logspath/access.log";
		$ctx->fs->file_put_contents($accesslog, '');
		$ctx->fs->chmod($accesslog, 0664);

		$ctx->cli->passthru("tail -f -n0 $accesslog");
	}

} # class
