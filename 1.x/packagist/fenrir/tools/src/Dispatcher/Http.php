<?php namespace fenrir\tools;

/**
 * @copyright (c) 2014, freia Team
 * @license BSD-2 <http://freialib.github.io/license.txt>
 * @package freia Library
 */
class HttpDispatcher implements \hlin\attribute\Contextual {

	use \hlin\ContextualTrait;

	/**
	 * @var \hlin\tools\RequestSignature
	 */
	protected $request = null;

	/**
	 * @var \hlin\tools\ResponseSignature
	 */
	protected $response = null;

	/**
	 * @var array
	 */
	protected $filters = [];

	/**
	 * @var boolean
	 */
	protected $singlematch = true;

	/**
	 * @var int
	 */
	protected $matches = 0;

	/**
	 * @var callable
	 */
	protected $on_success = null;

	/**
	 * @return static
	 */
	static function instance(\hlin\archetype\Context $context, callable $on_success = null) {
		$i = new static;
		$i->context_is($context);
		$i->request = \hlin\Request::instance($context);
		$i->response = \hlin\Response::instance($context);

		if ($on_success === null) {
			$i->on_success = function () { };
		}

		return $i;
	}

	/**
	 * @return \hlin\tools\RequestSignature
	 */
	function request() {
		return $this->request;
	}

	/**
	 * @return \hlin\tools\ResponseSignature
	 */
	function response() {
		return $this->response;
	}

// ---- Request Types ---------------------------------------------------------

	/**
	 * HTTP 1.1 GET method
	 */
	function get($route, callable $controller) {
		if ($this->request->requestMethod() == 'get') {
			$this->any($route, $controller);
		}
	}

	/**
	 * HTTP 1.1 POST method
	 */
	function post($route, callable $controller) {
		if ($this->request->requestMethod() == 'post') {
			$this->any($route, $controller);
		}
	}

	/**
	 * ...
	 */
	function any($route, callable $controller) {

		if ($this->singlematch && $this->matches > 0) {
			return;
		}

		$req = $this->request;
		$res = $this->response;

		if ($this->matches($route, $params)) {
			$this->matches++;
			$req->params_are($params);
			$body = $res->parse($controller($req, $res));
			$this->context->web->send($body, $res->responseCode(), $res->headers());
			$this->success();
		}
	}

	/**
	 * Function executed on successful dispatch.
	 * @codeCoverageIgnore
	 */
	protected function success() {
		call_user_func($this->on_success);
	}

	/**
	 * ...
	 */
	function matches($route, array &$params = null) {

		$req = $this->request;

		$route = rtrim($route, '/');

		if (empty($route)) {
			$route = '/';
		}

		$uri = $req->requestUri();

		return preg_match("#^$route$#", $uri, $params);
	}

	/**
	 * @return boolean
	 */
	function nomatch() {
		return $this->matches === 0;
	}

} # class
