<?php namespace example\routes;

function webapp_router($syspath, $routes, \fenrir\HttpDispatcher $http, $mysql) {

	$httpinfo = [
		'name' => 'Awesome',
		'year' => date('Y'),
		'api' => '/api/1.0'
	];

	$webapp = function ($req, $res) use ($httpinfo) {
		$res->conf('index');
		return [ 'server' => $httpinfo ];
	};

	$http->get($routes['index'], $webapp);
	$http->get($routes['thread'], $webapp);
	$http->get($routes['post'], $webapp);
	$http->get($routes['about'], $webapp);

}
