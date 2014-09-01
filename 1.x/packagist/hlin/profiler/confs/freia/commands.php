<?php return [

	'access-log' => [
		'topic' => 'hlin.profiler',
		'command' => 'hlin.AccessLogCommand',
		'flagparser' => 'hlin.NoopFlagparser',
		'summary' => 'view log of access control resolution',
		'desc' => 'Many access operations can be ambigous in how they resolve, this command helps demystefy their resolution by showing who/what/how got access in the system thereby exposing potential errors and helping in debug problematic rules.',
		'examples' => [
			"" => "View acceess log",
		],
		'help' => false # = command does not have any parameters
	],

]; # conf