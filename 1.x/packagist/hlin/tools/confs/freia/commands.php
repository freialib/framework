<?php return [

	// application commands
	// --------------------

	'?' => [
		'topic' => 'application',
		'command' => 'hlin.CmdhelpCommand',
		'flagparser' => 'hlin.NoopFlagparser',
		'summary' => 'show information for a command',
		'desc' =>
			"The ? command allows you to show detailed information on a command.",
		'examples' => [
			"some:command" => "Display usage information for some:command",
			"help" => "Display usage information for help command",
			"?" => "Display usage information for self",
			"" => "Short form for display usage information for self"
		],
		'help' =>
			" :invokation [commandname]\n\n".
			"  Get help on the specified command."
	],

	'help' => [
		'topic' => 'application',
		'command' => 'hlin.HelpCommand',
		'flagparser' => 'hlin.NoopFlagparser',
		'summary' => 'general help information and list command topics',
		'examples' => [
			"" => "Display version, usage and all command topics",
			"application" => "List just application topic",
			"some.topic" => "List just some.topic",
			"-unsorted-" => "List commands with out a topic",
		],
		'help' =>
			" :invokation [commandtopic]\n\n".
			"  Filter help to specified command group."
	],

	'version' => [
		'topic' => 'application',
		'command' => 'hlin.VersionCommand',
		'flagparser' => 'hlin.NoopFlagparser',
		'summary' => 'version information',
		'examples' => [
			"" => "Display version",
		],
		'help' =>
			" :invokation\n\n".
			"  Show system versions."
	],

	'honeypot' => [
		'topic' => 'application',
		'command' => 'hlin.HoneypotCommand',
		'flagparser' => 'hlin.NoopFlagparser',
		'summary' => 'generate autocomplete honeypots',
		'desc' =>
			"The honeypot command scans your files and generates an IDE honeypot in your cache directory. ".
			"You must ensure your IDE can read it. You may also need to open the file manually if your IDE is slow to update.\n\n".
			"If the command succesfully runs you'll have autocomplete support.\n\n".
			"Please note that if the class in question wasn't properly documented, such as missing @return statements, autocomplete may still not work in those cases.",
		'examples' => [
			"" => "Regenerate all honeypots",
			"fenrir.tools" => "Rengenerate fenrir.tools",
			"fenrir.tools hlin.canon hlin.attributes" => "Rengenerate fenrir.tools, hlin.canon and hlin.attributes",
		],
		'help' =>
			" :invokation [namespaces...]\n\n".
			"    Generate the honeypot files. Optionally namespaces, in universal\n".
			"    notation, may be passed. If none are passed all namespaces will\n".
			"    be generated.\n\n"
	],

	'log' => [
		'topic' => 'application',
		'command' => 'hlin.LogCommand',
		'flagparser' => 'hlin.NoopFlagparser',
		'summary' => 'track logs summary',
		'desc' =>
			"This command is just a shorthand for tail -f -n0 path/to/logs/summary.log\n".
			"If summary log doesn't exist it will be created.",
		'examples' => [
			"" => "show logs summary",
		],
		'help' => false # = command does not have any parameters
	],

	'module-stack' => [
		'topic' => 'application',
		'command' => 'hlin.ModuleStackCommand',
		'flagparser' => 'hlin.NoopFlagparser',
		'summary' => 'show enabled modules in loading order',
		'examples' => [
			"" => "show modules stack order; from highest priority to lowest",
		],
		'help' => false # = command does not have any parameters
	],

]; # conf
