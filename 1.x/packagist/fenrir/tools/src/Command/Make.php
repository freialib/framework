<?php namespace fenrir\tools;

/**
 * @copyright (c) 2014, freia Team
 * @license BSD-2 <http://freialib.github.io/license.txt>
 * @package freia Library
 */
class MakeCommand implements \hlin\archetype\Command {

	use \hlin\CommandTrait;

	/**
	 * @return int
	 */
	function main(array $args = null) {
		$cli = $this->cli;
		// did we get a parameter?
		if (count($args) == 0) {
			$cli->printf("You must specify the thing which you wish to create.");
			return 500;
		}
		$thing = array_shift($args);
		// did we get the query request?
		if ($thing == '?') {
			$cli->printf(implode("\n", $this->supported_domains()));
			return 0;
		}
		// did we get a domain?
		if (($ptr = stripos($thing, ':')) != false) {
			$domain = substr($thing, 0, $ptr);
			$thing  = substr($thing, $ptr + 1);
		}
		else { // no domain; try to guess
			$domain = $this->guess_domain($thing);
			// handle error state
			if ($domain === null) {
				$cli->printf("Failed to guess the problem domain. Please use the domain syntax.");
				return 500;
			}

			$cli->printf("\n Assuming domain: $domain\n\n");
		}

		if ( ! in_array($domain, $this->supported_domains())) {
			$cli->printf("The problem domain $domain is not supported.");
			return 500;
		}

		// get solution
		$solution = $this->problem_solution($domain, $thing);

		// verify solution
		if ($solution === false) {
			$cli->printf("There is currently no available solution for the problem.");
			return 500;
		}
		else if ($solution === null || empty($solution)) {
			$cli->printf("Failed to solve problem.");
			return 500;
		}

		// write out actions
		$cli->printf("\n Command will perform the following:\n\n");
		foreach (array_keys($solution) as $desc) {
			$step = wordwrap(str_replace("\n", "\n    ", trim($desc, "\n\r\t ")), 75, "\n    ");
			$cli->printf("  - $step\n\n");
		}

		$cli->printf("\n");

		if ('n' == $cli->ask(" Is the solution okey?", ['Y', 'n'])) {
			$cli->printf("Terminating. Bye.");
			return 500;
		}

		// solution accepted, execute
		$cli->printf("\n Executing:\n\n");
		foreach ($solution as $desc => $action) {
			$step = wordwrap(str_replace("\n", "\n    ", trim($desc, "\n\r\t ")), 75, "\n    ");
			$cli->printf("  - $step\n\n");
			if ( ! $action()) {
				$cli->printf("FAILED. Error execute step! Terminating.");
				return 500;
			}
		}

		return 0;
	}

// ---- Private ---------------------------------------------------------------

	/**
	 * @return array
	 */
	protected function supported_domains() {
		return array
			(
				'class',
			);
	}

	/**
	 * @return array ( description[String] => action[callable] )
	 */
	protected function problem_solution($domain, $thing) {
		if ($domain == 'class') {
			return $this->resolve_class($thing);
		}

		return false;
	}

	/**
	 * @return array ( description[String] => action[callable] )
	 */
	protected function resolve_class($thing) {
		$cli = $this->cli;

		// intentionally not mitigating slashes
		if (($ptr = strrpos($thing, '.')) == false) {
			$cli->printf("Failed to find namespace. Did you use \ notation instead of . notation?\n");
			return null;
		}

		$namespace = substr($thing, 0, $ptr);
		$name = substr($thing, $ptr + 1);

		$filename = str_replace('_', '/', $name);
		$dirbreak = strlen($name) - strcspn(strrev($name), 'ABCDEFGHJIJKLMNOPQRSTUVWXYZ') - 1;

		if ($dirbreak > 0 && $dirbreak != strlen($name) - 1) {
			$archtype = substr($name, $dirbreak);
			$filename = $archtype.'/'.substr($name, 0, $dirbreak);
		}
		else { // dirbreak == 0
			$archtype = null;
			$filename = $name;
		}

		$modulepaths = $this->context->autoloader()->paths();

		if ( ! isset($modulepaths[$namespace])) {
			$cli->printf("The namespace $namespace is not available.\n");
			return null;
		}

		$modulepath = $modulepaths[$namespace];
		$classpath = "$modulepath/src/$filename.php";

		$normalizedpath = str_replace(DIRECTORY_SEPARATOR, '/', $classpath);
		if (($ptr = strrpos($normalizedpath, '/')) !== false) {
			$classdirpath = substr($classpath, 0, $ptr);
		}
		else { // failed to detect directory
			throw new Panic("Failed to detect directory for $classpath");
		}

		$rootpath = $this->context->path('rootpath');

		if (file_exists($classpath)) {
			$friendlyclasspath = str_replace(DIRECTORY_SEPARATOR, '/', str_replace($rootpath, 'rootpath:', $classpath));
			$cli->printf("The file $friendlyclasspath already exists.\n");
			return null;
		}

		$phpnamespace = ltrim(\hlin\PHP::pnn($namespace), '\\');

		if (($firstptr = stripos($namespace, '.')) != false) {
			$group = substr($namespace, 0, $firstptr);
		}
		else { // namespace does not have more then one segment
			$group = $namespace;
		}

		$classfile
			= "<?php namespace $phpnamespace;\n\n"
			. "/**\n * ...\n */\n"
			. "class $name"
			;


		$shortclasspath = str_replace('\\', '/', str_replace($rootpath, 'rootpath:', $classpath));

		$create_class_desc = "write in $shortclasspath the class \\$phpnamespace\\$name";

		$archtypes = $this->confs->read('freia/make/patterns');
		if ($archtype !== null && isset($archtypes[$archtype])) {
			$ci = $archtypes[$archtype]; # class information
			$doc_parts = [];
			if ( ! empty($ci['extends'])) {
				$classfile .= " extends {$ci['extends']}";
				$doc_parts[] = " that extends {$ci['extends']}";
			}
			if ( ! empty($ci['implements'])) {
				$interfaces = implode(', ', array_map(function ($i) { return \hlin\PHP::pnn($i); }, $ci['implements']));
				$classfile .= " implements $interfaces";
				$doc_parts[] = (count($doc_parts) == 0 ? ' that ' : ' ')."implements $interfaces";
			}
			$classfile .= " {\n";
			if ( ! empty($ci['use'])) {
				$classfile .= "\n\tuse ".implode(";\n\tuse ", array_map(function ($i) { return \hlin\PHP::pnn($i); }, $ci['use'])).";\n";
				$traits = implode(', ', $ci['use']);
				if (count($ci['use']) == 1) {
					$doc_parts[] = (count($doc_parts) == 0 ? ' that ' : ' ')."uses the trait $traits";
				}
				else { // more then one trait
					$doc_parts[] = (count($doc_parts) == 0 ? ' that ' : ' ')."uses the traits $traits";
				}
			}

			if (count($doc_parts) > 1) {
				$lastitem = array_pop($doc_parts);
				$create_class_desc .= implode(',', $doc_parts).' and'.$lastitem;
			}
			else if (count($doc_parts) == 1) {
				$create_class_desc .= $doc_parts[0];
			}

			if (isset($ci['placeholder'])) {
				$classfile .= "\n".trim($ci['placeholder'], "\n")."\n\n";
			}
			else {
				$classfile .= "\n\t// TODO ($group): implement $thing\n\n";
			}
		}
		else { // no special archtype
			$classfile .= " {\n";
			$classfile .= "\n\t// TODO ($group): implement $thing\n\n";
		}

		$classfile .= "} # class\n";

		$fs = $this->fs;

		$actions = [];

		if ( ! file_exists($classdirpath)) {
			$friendlyclassdirpath = str_replace(DIRECTORY_SEPARATOR, '/', str_replace($rootpath, 'rootpath:', $classdirpath));
			$actions["create the directory path $friendlyclassdirpath in mode 0770"]
				= function () use ($fs, $classdirpath) {
					return $fs->mkdir($classdirpath, 0770, true);
				};
		}

		$actions[$create_class_desc]
			= function () use ($fs, $classfile, $classpath) {
				return $fs->file_put_contents($classpath, $classfile);
			};

		$honeypot_command = \hlin\HoneypotCommand::instance($this->context);

		$actions["refresh honeypot for $namespace"]
			= function () use ($honeypot_command, $namespace, $cli) {
				$cli->echoOff();
				$status = $honeypot_command->main([$namespace]);
				$cli->echoOn();
				return $status == 0;
			};

		return $actions;
	}

	/**
	 * Returns domain, or null if no domain can be resolved.
	 *
	 * @return string|null
	 */
	protected function guess_domain($thing) {
		if (strpos($thing, '.') !== false) {
			return 'class';
		}
		else { // unknown
			return null;
		}
	}

} # class
