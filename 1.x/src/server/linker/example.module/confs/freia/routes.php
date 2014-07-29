<?php

	$idRgx = '[0-9]+';

	# all IDs should have a qualified name, never used "id" in a route

	$thread = "(?P<thread>$idRgx)";
	$post = "(?P<post>$idRgx)";
	$forum = "(?P<forum>$idRgx)";

return array
	(
		'index'  => '/',
		'thread' => "/thread/$thread",
		'post'   => "/post/$post",
		'about'  => '/about',

		'api-v1.0' => array
			(
				// forums
				'forum' => "/api/1.0/forums/$forum",

				// threads
				'forum/threads' => "/api/1.0/forums/$forum/threads",
				'threads' => '/api/1.0/threads',
				'thread' => "/api/1.0/threads/$thread",

				// posts
				'thread/posts' => "/api/1.0/threads/$thread/posts",
				'posts' => '/api/1.0/posts',
			),

	); # conf
