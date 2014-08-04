<?php return [

	'description'
		=> 'Install for example.',

	'configure' => [
		'tables' => [
			'todos',
		]
	],

	'create:tables' => [

		'todos' =>
			'
				_id   [primaryKey],
				title [title],

				PRIMARY KEY (_id)
			',

	],

]; # conf
