<?php namespace app\main;

/**
 * ...
 */
function router($syspath, \fenrir\HttpDispatcher $http, \hlin\archetype\Context $context, \fenrir\system\MysqlDatabaseSignature $mysql) {

	$routespath = realpath(__DIR__);
	$routes = $context->confs->read('freia/routes');

	// we handle api and webapp requests using different logic
	if ($http->matches('/api/.*')) {

		try {

			// fatalerror handling
			register_shutdown_function(function () {
				$death = error_get_last();
				if ($death !== null) {
					http_response_code(500);
					echo json_encode(['error' => "Internal server error" ]);
				}
			});

			// router logic
			$http->response()->logic(function ($response, $conf) {
				return json_encode($response);
			});

			// run router
			require "$routespath/api/1.0.php";
			\example\routes\api_v1_router($syspath, $routes, $http, $mysql);

			// 404, won't match if anything else matched before
			$http->any('/api/.*', function ($req, $res) {
				$res->responseCode(404);
				return ['error' => 'Requested API version not found.'];
			});

			return 0;
		}
		catch (\Exception $e) {
			$context->logger->logexception($e);
			$context->web->send(json_encode(['error' => 'System has encountered an error. Request terminated.']), 500);
			return 0;
		}

	}
	else { // webapp pages

		try {

			// fatalerror handling
			register_shutdown_function(function () {
				$death = error_get_last();
				if ($death !== null) {
					http_response_code(500);
					echo \app\theme\main('public:500');
				}
			});

			// router logic
			$http->response()->logic(function ($response, $conf) {
				return \app\theme\main("webapp:$conf", $response);
			});

			// run router
			require "$routespath/public/webapp.php";
			\example\routes\webapp_router($syspath, $routes, $http, $mysql);

			// 404
			if ($http->nomatch()) {
				return 404;
			}

			return 0;
		}
		catch (\Exception $e) {
			$context->logger->logexception($e);
			return 500;
		}
	}

}
