<?php namespace example\routes;

function webapp_router($domain, $syspath, $routes, \fenrir\HttpDispatcher $http, $mysql) {

	$httpinfo = [
		'sitetitle' => 'Awesome',
		'wwwpath' => $domain['wwwpath'],
		'year' => date('Y'),
		'api' => '/api/1.0',
		'baseurl' => $domain['baseurl']
	];

	$webapp = function ($req, $res) use ($httpinfo) {
		$res->conf('index');
		return [ 'server' => $httpinfo ];
	};

	$http->get('/', $webapp);

}
