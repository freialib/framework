<?php namespace hlin\attribute;

/**
 * @copyright (c) 2014, freia Team
 * @license BSD-2 <http://freialib.github.io/license.txt>
 * @package freia Library
 */
trait ContextualTrait {

	/**
	 * @return static
	 */
	static function instance(\hlin\archetype\Context $context) {
		$i = new static;
		$i->context_is($context);
		return $i;
	}

	/**
	 * @var \hlin\archetype\Context
	 */
	protected $context = null;

	/**
	 * @var \hlin\archetype\Configs
	 */
	protected $confs = null;

	/**
	 * @var \hlin\archetype\FileSystem
	 */
	protected $fs = null;

	/**
	 * @var \hlin\archetype\CLI
	 */
	protected $cli = null;

	/**
	 * @var \hlin\archetype\Logger
	 */
	protected $logger = null;

	/**
	 * @codeCoverageIgnore
	 * @return static $this
	 */
	function context_is(\hlin\archetype\Context $context) {

		$this->context = $context;

		// this convention is used here simply because it saves A LOT of code
		// makes a lot of code be a lot more readable; pattern should not be
		// copied with tacking that into consideration

		$this->logger = & $context->logger;
		$this->confs = & $context->confs;
		$this->cli = & $context->cli;
		$this->fs = & $context->fs;
		$this->web = & $context->web;

		return $this;
	}

} # trait
