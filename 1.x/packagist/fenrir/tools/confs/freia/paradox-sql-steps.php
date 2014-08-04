<?php return [

	'class' => function ($op) {
		$op = str_replace(':tables', '', $op);
		return 'fenrir.Sql'.ucfirst($op).'Pdx';
	},

	'logic' => [
		// setup the environment
		'configure' => 100,
		// remove deprecated elements; such as bindings
		'cleanup:tables' => 200,
		// create tables
		'create:tables' => 300,
		// modify existing tables
		'modify:tables' => 400,

	// ----------------------------------------------------------------

		// perform bindings to other tables
		'bindings' => 1000,
		// perform post-cleanup
		'normalize' => 2000,
		// perform any fixes to the database entries
		'fixes' => 3000,
		// populate tables with data
		'populate' => 4000,
	]

]; # conf
