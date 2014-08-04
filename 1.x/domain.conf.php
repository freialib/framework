<?php return array_merge (
	include realpath(__DIR__.'/../domain.conf.php'), [

		#
		# Development Version of domain.conf.php
		# ---------------------------------------
		# Use this for special settings during development. In development
		# your server should point to stage as the public root.
		#
		# Please use a local domain.conf.php when developing so as to not
		# force your own local server settings on other developers working on
		# the project
		#

		// what kind of environment is this?
		'environment' => 'development',

		// where is server/system synced to?
		'systempath' => realpath(__DIR__.'/src/server/system'),

		// where is the project root located?
		'prjpath' => realpath(__DIR__),

		// where should the project logs be stored?
		'logspath' => realpath(__DIR__.'/files/logs'),

		// where should cache and temporary files be stored?
		'cachepath' => realpath(__DIR__.'/files/cache'),

		// what is the base url of the site?
		'baseurl' => '',

	]

); # conf
