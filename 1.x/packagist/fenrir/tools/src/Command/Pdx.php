<?php namespace fenrir\tools;

/**
 * @copyright (c) 2014, freia Team
 * @license BSD-2 <http://freialib.github.io/license.txt>
 * @package freia Library
 */
class PdxCommand implements \hlin\archetype\Command {

	use \hlin\CommandTrait;

	/**
	 * @return int
	 */
	function main(array $args = null) {

		$cli = $this->cli;

		if (empty($args)) {
			$cli->printf("Incorrect command invokation.");
			return 500;
		}

		$command = array_shift($args);

		if ($command == 'list') {
			return $this->databases();
		}

		if (empty($args)) {
			$cli->printf("Missing database.");
			return 500;
		}

		$dbname = array_shift($args);

		// args is passed on even if it isn't required to allow for overwrites
		// to use any additional parameters they might add

		if ($command == 'init') {
			return $this->init($dbname, $args);
		}
		else if ($command == 'sync') {
			return $this->sync($dbname, $args);
		}
		else if ($command == 'log') {
			return $this->log($dbname, $args);
		}
		else if ($command == 'rm') {
			return $this->rm($dbname, $args);
		}
		else { // unknown command
			$cli->printf("Unrecognized command.");
			return 500;
		}

		return 0;
	}

	/**
	 * "list" command
	 */
	function databases() {

		$cli = $this->cli;
		$dbs = $this->confs->read('freia/databases');

		foreach (array_keys($dbs) as $key) {
			$cli->printf("  $key\n");
		}

		return 0;
	}

	/**
	 * ...
	 */
	function log($database, $args) {

		$cli = $this->cli;

		if (($pdx = $this->pdx($database, $this->is_verbose($args))) === null) {
			$cli->printf("Failed to create paradox.");
			return 500;
		}

		if (in_array('--detailed', $args)) {
			$detailed = true;
		}
		else { // summary
			$detailed = false;
		}

		if (in_array('--sigs', $args)) {
			$signatures = true;
		}
		else { // summary
			$signatures = false;
		}

		list($log, $err) = $pdx->history();

		$cli->printf("\n");

		if ($err != 0) {
			$cli->printf("Failed to get history.\n");
			return $err;
		}

		if (empty($log)) {
			$cli->printf("\n No history.\n");
		}
		else { // display history
			$format = " %4s  %-10s  %-20s  %-7s  %s\n";
			$cli->printf($format, 'step', 'timestamp', 'channel', 'version', 'hotfix');
			$cli->printf
				(
					$format,
					str_repeat('-', 4),
					str_repeat('-', 10),
					str_repeat('-', 20),
					str_repeat('-', 7),
					str_repeat('-', 15)
				);

			if ($detailed) {
				$cli->printf("\n");
			}

			foreach ($log as $i) {
				$cli->printf
					(
						$format,
						$i['id'].'.',
						date('Y-m-d', strtotime($i['timestamp'])),
						$i['channel'],
						$i['version'],
						$i['hotfix'] !== null ? $i['hotfix'] : 'no'
					);

				if ($detailed) {
					$cli->printf("\n".str_repeat(' ', 7).wordwrap($i['description'], 70)."\n");
				}

				if ($signatures) {
					$cli->printf("\n".str_repeat(' ', 7).wordwrap($i['system'], 70)."\n");
				}

				if ($signatures || $detailed) {
					$cli->printf("\n");
				}
			}
		}

		return 0;
	}

	/**
	 * ...
	 */
	function init($database, $args) {

		$cli = $this->cli;

		if (($pdx = $this->pdx($database, $this->is_verbose($args))) === null) {
			$cli->printf("Failed to create paradox.");
			return 500;
		}

		if (in_array('!', $args)) {
			$dryrun = false;
		}
		else { // ! not in $args
			$dryrun = true;
			$this->dryrun_disclaimer();
		}

		$cli->printf("\n");
		list($res, $err) = $pdx->init($dryrun);

		if ($dryrun) {

			$this->rm_print_dryrun($database, $res['rm']);

			$cli->printf("\n");

			if ($res['history'] !== null) {
				foreach ($res['history'] as $entry) {
					$cli->printf
						(
							" %9s %s %s\n",
							$entry['version'],
							$entry['channel'],
							empty($entry['hotfix']) ? '' : '/ '.$entry['hotfix']
						);
				}
			}
		}
		else { // not dry run
			$cli->printf("\n\n  Initialization complete.\n");
		}

		return $err;
	}

	/**
	 * ...
	 */
	function rm($database, $args) {

		$cli = $this->cli;

		if (($pdx = $this->pdx($database, $this->is_verbose($args))) === null) {
			$cli->printf("Failed to create paradox.");
			return 500;
		}

		if (in_array('!', $args)) {
			$dryrun = false;
		}
		else { // ! not in $args
			$dryrun = true;
			$this->dryrun_disclaimer();
		}

		if (in_array('--hard', $args)) {
			$harduninstall = true;
		}
		else { // --hard not in flags
			$harduninstall = false;
		}

		$cli->printf("\n");

		list($res, $status) = $pdx->rm($dryrun, $harduninstall);

		if ($status != 0) {
			$cli->printf(" rm operation denied!\n");
			return $status;
		}

		if ($dryrun) {
			$this->rm_print_dryrun($database, $res);
		}

		return $status;
	}

	/**
	 * ...
	 */
	function sync($database, $args) {

		$cli = $this->cli;

		if (($pdx = $this->pdx($database, $this->is_verbose($args))) === null) {
			$cli->printf("Failed to create paradox.");
			return 500;
		}

		if (in_array('!', $args)) {
			$dryrun = false;
		}
		else { // ! not in $args
			$dryrun = true;
			$this->dryrun_disclaimer();
		}

		list($res, $status) = $pdx->sync($dryrun);

		if ($status != 0) {
			$cli->printf("\n Sync denied!\n");
			return $status;
		}

		if ($dryrun) {
			$cli->printf("\n");
			if (empty($res)) {
				$cli->printf(" Nothing to sync.\n");
			}
			else { // ! empty result
				foreach ($res as $entry) {
					$cli->printf(" %9s %s %s\n", $entry['version'], $entry['channel'], empty($entry['hotfix']) ? '' : '/ '.$entry['hotfix']);
				}
			}
		}
		else { // dryrun == false

			if ($res > 0) {
				$cli->printf("\n\n");

			}

			$cli->printf(" Sync complete.\n");
		}

		return $status;
	}

// ---- Private ---------------------------------------------------------------

	/**
	 * ...
	 */
	protected function dryrun_disclaimer() {
		$this->cli->printf("\n Dry run; please verify the command will produce the desired effect.\n Add \"!\" at the end of the command to execute.\n");
	}

	/**
	 * ...
	 */
	protected function rm_print_dryrun($database, $res) {

		$cli = $this->cli;

		if (empty($res)) {
			$cli->printf(" Nothing to remove.\n");
		}
		else { // ! empty result
			foreach ($res as $table) {
				$cli->printf(" rm $database -> $table\n");
			}
		}
	}

	/**
	 * @return \fenrir\Pdx
	 */
	protected function pdx($database, $verbose = true) {

		$conf = $this->confs->read('freia/paradox');

		if (($ptr = strrpos($database, '.')) !== false) {
			$dbtype = substr($database, $ptr + 1);
			$dbtype = $this->normalizeDbType($dbtype);
		}
		else { // failed to detect dbtype
			$dbtype = 'sql';
		}

		$constants = $this->confs->read("freia/paradox-$dbtype-constants");
		$dbconf = $this->confs->read('freia/databases');
		$cli = $this->cli;

		if ( ! isset($dbconf[$database])) {
			return null;
		}

		$logger = function ($message) use ($cli) {
			$cli->printf($message);
		};

		return \fenrir\Pdx::instance($this->context, $dbconf[$database], $conf, $constants, $logger, $verbose);
	}

	/**
	 * @return boolean true if verbose output, false otherwise
	 */
	protected function is_verbose($args) {
		return in_array('--verbose', $args)
			|| in_array('-v', $args);
	}

	/**
	 * Converts from db engine to db type if necesary or returns back as-is on
	 * failure.
	 *
	 * @return string
	 */
	protected function normalizeDbType($dbtype) {
		// is it a sql database type?
		if (in_array($dbtype, ['mysql', 'mariadb'])) {
			return 'sql';
		}

		// we failed...
		return $dbtype;
	}

} # class
