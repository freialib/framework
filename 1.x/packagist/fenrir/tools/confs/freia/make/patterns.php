<?php return [

	'Command' => [
		'extends' => null,
		'implements' => [ 'hlin.archetype.Command' ],
		'use' => [ 'hlin.CommandTrait' ],
		'placeholder' =>
			"\t/**\n\t * @return int\n\t */\n".
			"\tfunction main(array \$args = null) {\n".
			"\t\t// TODO implement\n".
			"\t}"
	],

	'Flagparser' => [
		'extends' => null,
		'implements' => [ 'hlin.tools.FlagparserSignature' ],
		'use' => [ 'hlin.FlagparserTrait' ]
	],

]; # config
