<?php return [

	'make' => [
		'topic' => 'fenrir.tools',
		'command' => 'fenrir.MakeCommand',
		'flagparser' => 'hlin.NoopFlagparser',
		'summary' => 'make a class, or just about anything else',
		'desc' =>
			"The make command will try to figure out what you want to make and create it as apropriatly as possible. ".
			"What you pass to it is considered the 'problem' and is what the command is trying to solve. A solution will be presented before executing it.\n\n".
			"If it's a class it will try to place it in the right namespace, under the right directory structure and have it implement the right interfaces.\n\n".
			"You can skip the guessing by providing a domain to the problem using the domain:problem syntax. Please see examples for details. ".
			"Some patterns may always require the domain to work.",
		'examples' => [
			'?' => "Get available domains",
			'example.FooCommand' => "Create class FooCommand (that implements \hlin\archetype\Command) with namespace example; place it in the file /Command/Foo.php located in the class path for the module specific to namespace example",
			'class:example.FooCommand' => 'Same as above only we ensure that the problem example.FooCommand is interpreted as a class'
		],
		'help' =>
			" :invokation ?\n\n".
			"    Get all supported problem domains.\n\n".
			" :invokation [<domain>:]<problem>\n\n".
			"    Solve the given problem. Domain is optional.\n".
			"    If domain is not provided the command will try to guess."
	],

	'conf' => [
		'topic' => 'fenrir.tools',
		'command' => 'fenrir.ConfCommand',
		'flagparser' => 'hlin.NoopFlagparser',
		'summary' => 'manage configuration files',
		'desc' =>
			"The conf command is used to manage configuration files and values. ".
			"You can use it to check your configuration, debug for errors, etc. ",
		'examples' => [
			'scan' => 'Scan for all configuration paths',
			'show freia/make/class-patterns' => 'Show computed configuration value',
			'all freia/make/class-patterns' => 'Show all individual configuration values',
			'files freia/make/class-patterns' => 'Show all files used to generate config'
		],
		'help' =>
			" :invokation <command> [<configuration>]\n\n".
			" Commands:\n\n".
			"  scan  - scan for all configuration files\n".
			"  show  - show configuration\n".
			"  all   - show all component configuration files\n".
			"  files - show all files used to create the configuration\n\n".
			" All commands except scan accept a mandatory configuration parameter."
	],

	'pdx' => [
		'topic' => 'fenrir.tools',
		'command' => 'fenrir.PdxCommand',
		'flagparser' => 'hlin.NoopFlagparser',
		'summary' => 'manage database migrations',
		'desc' =>
			"The paradox (pdx) command helps you sync your source data structure to your database data structure though migrations that you define.\n\n".
			"The paradox system is capable of handling a modular system.",
		'examples' => [
			"list" => "View databases known to system",
			"log demo.mysql" => "Show history of the demo.mysql database",
			"init demo.mysql" => "Dry-run for setup of demo.mysql for the first time",
			"init demo.mysql !" => "Setup demo.mysql for the first time",
			"rm demo.mysql" => "Dry-run for deletion of demo.mysql",
			"rm demo.mysql !" => "Delete demo.mysql as it's know by history",
			"rm demo.mysql --hard !" => "Hard delete demo.mysql",
			"sync demo.mysql" => "Dry-run for syncing the demo.mysql database",
			"sync demo.mysql !" => "Sync demo.mysql database",
		],
		'help' =>
			" :invokation list\n\n".
			"    List known databases.\n\n".
			" :invokation log [database]\n\n".
			"    View database history of changes.\n\n".
			" :invokation init [database] [!]\n\n".
			"    Install a database for the first time.\n\n".
			" :invokation sync [database] [!]\n\n".
			"    Looks at migrations known by system and applies missing ones.\n\n",
	],

]; # conf
