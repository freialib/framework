<?php namespace example\routes;

/**
 * ...
 */
function api_v1_router($syspath, $routes, \fenrir\HttpDispatcher $http, $mysql) {

	$api = $routes['api-v1.0'];

#### Forum ####################################################################

	$http->get($api['forum'], function ($req, $res) {

		$forum = \example\Forum::instance([
			'_id' => 1,
			'title' => 'Main Forum',
			'slugid' => 'mainforum'
		]);

		return $forum->toArray();
	});

#### Threads ##################################################################

	// get threads based on forum
	$http->get($api['forum/threads'], function ($req, $res) use ($mysql) {
		$repo = \example\ThreadRepo::instance($mysql);
		$threads = $repo->find([
			'forum' => $req->param('forum')
		]);
		return array_map (
			function ($thread) {
				return $thread->toArray();
			},
			$threads
		);
	});

	// get a single thread
	$http->get($api['thread'], function ($req, $res) use ($mysql) {
		$thread_id = $req->param('thread');
		$repo = \example\ThreadRepo::instance($mysql);
		return $repo->entry($thread_id)->toArray();
	});

	// create new thread
	$http->post($api['threads'], function ($req, $res) use ($mysql) {

		$input = $req->input(['json', 'post']);
		$entry = \example\Thread::instance([
			'title' => $input['title'],
			'forum' => $input['forum']
		]);

		$repo = \example\ThreadRepo::instance($mysql);
		$new_entry = $repo->store($entry);

		return $new_entry->toArray();
	});

#### Posts ####################################################################

	// get posts based on thread
	$http->get($api['thread/posts'], function ($req, $res) use ($mysql) {
		$repo = \example\PostRepo::instance($mysql);
		$posts = $repo->find([
			'thread' => $req->param('thread')
		]);
		return array_map (
			function ($post) {
				return $post->toArray();
			},
			$posts
		);
	});

	// create new post
	$http->post($api['posts'], function ($req, $res) use ($mysql) {

		$input = $req->input(['json', 'post']);
		$entry = \example\Post::instance([
			'body' => $input['body'],
			'thread' => $input['thread']
		]);

		$repo = \example\PostRepo::instance($mysql);
		$new_entry = $repo->store($entry);

		return $new_entry->toArray();
	});

#### 404 ######################################################################

	$http->any('/api/1.0/.*', function ($req, $res) {
		$res->responseCode(404);
		return ['error' => 'Requested v1.0 API not found.'];
	});

}
