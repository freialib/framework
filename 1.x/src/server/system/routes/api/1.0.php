<?php namespace example\routes;

/**
 * ...
 */
function api_v1_router($domain, $syspath, $routes, \fenrir\HttpDispatcher $http, $mysql) {

	$api = $routes['api-v1.0'];

#### Todos ####################################################################

	$http->get($api['todos'], function ($req, $res) {

		$todos = \example\Todos::instance([
			'title' => 'Main Todo List',
		]);

		return $todos->toArray();
	});

#### 404 ######################################################################

	$http->any('/api/1.0/.*', function ($req, $res) {
		$res->responseCode(404);
		return ['error' => 'Requested v1.0 API not found.'];
	});

}
