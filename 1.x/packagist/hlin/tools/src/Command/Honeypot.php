<?php namespace hlin\tools;

/**
 * @copyright (c) 2014, freia Team
 * @license BSD-2 <http://freialib.github.io/license.txt>
 * @package freia Library
 */
class HoneypotCommand implements \hlin\archetype\Command {

	use \hlin\CommandTrait;

	/**
	 * @return int
	 */
	function main(array $args = null) {

		$cli = $this->cli;
		$fs = $this->fs;

		$autoloader = $this->context->autoloader();

		if ($autoloader === null) {
			throw new Panic('Honeypot command requires autoloader to be defined in context.');
		}

		$paths = $autoloader->paths();
		$namespaces = array_keys($paths);

		$cachepath = $this->context->path('cachepath', true);

		if ($cachepath == null) {
			$cli->printf("The cachepath path is not defined in the current system.\n");
			return 500;
		}

		if (count($args) > 0) {
			foreach ($args as $namespace) {
				if (in_array($namespace, $namespaces)) {
					$file = $this->generate_honeypot($namespace, $paths[$namespace]);
					$honeypotpath = "$cachepath/honeypots";
					if ( ! $fs->file_exists($honeypotpath)) {
						$fs->mkdir($honeypotpath, 0770, true);
					}
					$fs->file_put_contents("$cachepath/honeypots/$namespace.php", $file);
					$cli->printf("  -> $namespace\n");
				}
			}

			return 0;
		}
		else { // regenerate all
			foreach ($paths as $namespace => $path) {
				$file = $this->generate_honeypot($namespace, $path);
				$honeypotpath = "$cachepath/honeypots";
				if ( ! $fs->file_exists($honeypotpath)) {
					$fs->mkdir($honeypotpath, 0770, true);
				}
				$fs->file_put_contents("$cachepath/honeypots/$namespace.php", $file);
				$cli->printf("  -> $namespace\n");
			}

			return 0;
		}
	}

// ---- Private ---------------------------------------------------------------

	/**
	 * @return string
	 */
	function generate_honeypot($namespace, $path) {

		$fs = $this->fs;

		$classes = [];
		$traits = [];

		$dirs = $fs->glob("$path/src/*", GLOB_ONLYDIR);
		foreach ($dirs as $dir) {
			$dir = $fs->basename($dir);
			$symbols = $fs->glob("$path/src/$dir/*.php");
			foreach ($symbols as $symbol) {
				$basename = preg_replace('/\.php$/', '', str_replace("$path/src/$dir/", '', $symbol));
				if ($dir == 'Trait') {
					$traits[] = "$basename$dir";
				}
				else { // not a trait
					// check if interface (interfaces must always be explicit)
					$symbolname = \hlin\PHP::pnn("$namespace.$basename$dir");
					if ( ! interface_exists($symbolname, true)) {
						$classes[] = "$basename$dir";
					}
				}
			}
		}

		$symbols = $fs->glob("$path/src/*.php");
		foreach ($symbols as $symbol) {
			$basename = preg_replace('/\.php$/', '', str_replace("$path/src/", '', $symbol));
			// check if interface (interfaces must always be explicit)
			$symbolname = \hlin\PHP::pnn("$namespace.$basename");
			if ( ! interface_exists($symbolname, true)) {
				$classes[] = "$basename";
			}
		}

		$ptr = stripos($namespace, '.');
		$basenamespace = ltrim(\hlin\PHP::pnn(substr($namespace, 0, $ptr)), '\\');
		$simplenamespace = ltrim(\hlin\PHP::pnn(substr($namespace, $ptr + 1)), '\\');

		$honeypot = "<?php # autocomplete IDE honeypot\n\n";
		$honeypot .= "namespace $basenamespace;\n\n";

		if ( ! empty($classes)) {
			$honeypot .= "// classes\n";
			foreach ($classes as $class) {
				$honeypot .= sprintf("class %-25s extends $simplenamespace\\$class {}\n", $class);
			}
			$honeypot .= "\n";
		}

		if ( ! empty($traits)) {
			$honeypot .= "// traits\n";
			foreach ($traits as $trait) {
				$honeypot .= sprintf("trait %-25s { use $simplenamespace\\$trait; }\n", $trait);
			}
			$honeypot .= "\n";
		}

		return $honeypot;
	}

} # class
