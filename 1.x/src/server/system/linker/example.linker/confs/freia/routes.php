<?php

	$idRgx = '[0-9]+';

	# all IDs should have a qualified name, never used "id" in a route

	$todo = "(?P<todo>$idRgx)";

return [

	'index'  => '/',

	'api-v1.0' => [
		'todos' => "/api/1.0/todos",
		'todo'  => "/api/1.0/todos/$todo",
	],

]; # conf
