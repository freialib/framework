<?php return array
	(
		'description'
			=> 'Install for example.',

		'configure' => array
			(
				'tables' => array
					(
						'forums',
						'threads',
						'posts'
					)
			),

		'create:tables' => array
			(
				'forums' =>
					'
						_id   [primaryKey],
						title [title],

						PRIMARY KEY (_id)
					',
				'threads' =>
					'
						_id   [primaryKey],
						title [title],

						PRIMARY KEY (_id)
					',
				'posts' =>
					'
						_id   [primaryKey],
						body  [block],

						PRIMARY KEY (_id)
					',
			),

	); # config
