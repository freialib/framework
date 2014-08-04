<?php namespace fenrir\tools;

/**
 * @copyright (c) 2014, freia Team
 * @license BSD-2 <http://freialib.github.io/license.txt>
 * @package freia Library
 */
class /* "Paradox" aka. */ Pdx  implements \hlin\attribute\Contextual {

	use \hlin\ContextualTrait;

	// version of class and associated features
	const VERSION = '2.0.0';

	/**
	 * @var string
	 */
	protected $dbtype = null;

	/**
	 * @var array
	 */
	protected $dbconf = null;

	/**
	 * @var array
	 */
	protected $conf = [];

	/**
	 * @var array
	 */
	protected $constants = [];

	/**
	 * @var array
	 */
	protected $stepconf = null;

	/**
	 * @var callable given a step name computes class name
	 */
	protected $stepclass = null;

	/**
	 * @var callable
	 */
	protected $writer = null;

	/**
	 * @var boolean
	 */
	protected $locked = false;

	/**
	 * @var boolean
	 */
	protected $verbose = true;

	/**
	 * @return static
	 */
	static function instance(\hlin\archetype\Context $context, array $dbconf, array $conf = [], array $constants = [], callable $logger = null, $verbose = true, $dbtype = 'mysql', array $stepconf = null) {

		$i = new static;
		$i->context_is($context);

		// Paradox configuration
		// ---------------------

		$nconf = []; # normalized configuration
		foreach ($conf as $channel => $versions) {
			$nconf[$channel] = [];
			foreach ($versions as $version => $value) {
				if (is_array($value)) {
					call_user_func_array([$i, 'conf'], $value);
				}
				else { // $value is not an array (assume string)
					$nconf[$channel][$version] = $i->conf($value);
				}
			}
		}

		$i->conf = $nconf;

		if ($stepconf === null && in_array($dbtype, $i->mysqlcompatibletypes())) {
			$stepconf = $context->confs->read('freia/paradox-sql-steps');
		}
		else if ($stepconf === null) {
			throw new Panic('Step configuration is mandatory.');
		}
		else { // got step configuration
			if ( ! isset($stepconf['class'], $stepconf['logic'])) {
				throw new Panic('Invalid step configuration given.');
			}
		}

		$i->stepclass = $stepconf['class'];
		$i->stepconf = $stepconf['logic'];

		$i->constants = $constants;

		// Database configuration
		// ----------------------

		$i->dbconf = $dbconf;

		if (static::supports($dbtype)) {
			$i->dbtype = $dbtype;
		}
		else { // dbtype not supported
			throw new Panic("Unsupported db type: $dbtype");
		}

		if (isset($dbconf['pdx:lock']) && $dbconf['pdx:lock'] == true) {
			$i->locked = true;
		}
		else { // pdx:lock == false
			$i->locked = false;
		}

		// Output / Logger Settings
		// ------------------------

		$i->verbose = $verbose;

		if ($logger === null) {
			$i->writer = function () {};
		}
		else { // logger !== null
			$i->writer = $logger;
		}

		return $i;
	}

	/**
	 * @return array relevant versions
	 */
	function versioninfo() {
		return [ \hlin\PHP::unn(__CLASS__) => self::VERSION ];
	}

// ---- Migration Methods -----------------------------------------------------

	/**
	 * @return array history table
	 */
	function history() {
		if ($this->has_history_table()) {

			$dbh = $this->dbh();

			$timeline_table = $this->table();
			$history = $dbh->prepare
				(
					"
						SELECT entry.*
						  FROM `$timeline_table` entry
					"
				)
				->execute()
				->fetch_all();
		}
		else { // no database
			$history = [];
		}

		return [$history, 0];
	}

	/**
	 * Initialize database
	 *
	 * @return array (status, result)
	 */
	function init($dryrun = true) {

		$has_history_table = $this->has_history_table();

		if ($this->locked && $has_history_table && ! $dryrun) {
			// operation is destructive and database is locked
			return [null, 401];
		}
		else { // database is not locked

			$channels = $this->channels();

			$status = [
				// ordered list of versions in processing order
				'history' => [],
				// current version for each channel
				'state' => [],
				// active channels
				'active' => [],
				// checklist of version requirements
				'checklist' => $this->generate_checklist($channels)
			];

			if ( ! $dryrun) {
				if ($has_history_table) {
					$this->rm();
				}
				else { // no history table available
					$this->log(" Skipped uninstall. Database is clean.\n");
				}
			}

			// generate version history for full reset
			foreach ($channels as $channel => & $timeline) {
				if (count($timeline['versions']) > 0) {
					end($timeline['versions']);
					$last_version = key($timeline['versions']);
					$this->processhistory($channel, $last_version, $status, $channels);
				}
			}

			// dry run?
			if ($dryrun) {

				if ($has_history_table) {
					list($rm_tables, $err) = $this->rm($dryrun);
				}
				else { // no history table available
					$rm_tables = [];
				}

				// just return the step history
				return [ [ 'history' => $status['history'], 'rm' => $rm_tables ], 0];
			}

			// execute the history
			foreach ($status['history'] as $entry) {
				// execute migration
				$this->processmigration($channels, $entry['channel'], $entry['version'], $entry['hotfix']);
			}

			// operation complete
			return [null, 0];
		}
	}

	/**
	 * Removes all tables.
	 *
	 * @return boolean true if successful, false if not permitted
	 */
	function rm($dryrun = false, $harduninstall = false) {

		if ($this->locked) {
			return [null, 400];
		}
		else { // database is not locked

			$channels = $this->channels();
			$config = [ 'tables' => [] ];

			if ( ! $harduninstall) {

				list($history) = $this->history();

				// generate table list based on history
				foreach ($history as $i) {
					if ($i['hotfix'] === null) {
						$handlers = $channels[$i['channel']]['versions'][$i['version']];
					}
					else { // hotfix
						$handlers = $channels[$i['channel']]['versions'][$i['version']]['hotfixes'][$i['hotfix']];
					}

					$this->rm__load_tables($config, $handlers);
				}
			}
			else { // hard uninstall
				foreach ($channels as $channelname => $chaninfo) {
					foreach ($chaninfo['versions'] as $version => $handlers) {
						$this->rm__load_tables($config, $handlers);
						if (isset($handlers['hotfixes'])) {
							foreach ($handlers['hotfixes'] as $hotfix => $fixhandlers) {
								$this->rm__load_tables($config, $fixhandlers);
							}
						}
					}
				}
			}

			if ($this->has_history_table()) {
				$config['tables'][] = $this->table();
			}

			if ($dryrun) {
				return [$config['tables'], 0];
			}

			if ( ! empty($config['tables'])) {
				$dbh = $this->dbh();
				$dbh->prepare('SET foreign_key_checks = FALSE')
					->execute();

				foreach ($config['tables'] as $table) {
					$this->log(" Removing $table\n");
					$dbh->prepare("DROP TABLE IF EXISTS `$table`")
						->execute();
				}

				$dbh->prepare('SET foreign_key_checks = TRUE')
					->execute();
			}
			else { // empty tables
				$this->log(" Nothing to remove.\n");
			}
		}

		return [null, 0];
	}

	/**
	 * Move the database forward.
	 */
	function sync($dryrun = false) {

		$channels = $this->channels();

		$status = [
			// ordered list of versions in processing order
			'history' => [],
			// current version for each channel
			'state' => [],
			// active channels
			'active' => [],
			// checklist of version requirements
			'checklist' => $this->generate_checklist($channels)
		];

		// inject current history
		list($history, $err) = $this->history();

		if ($err !== 0) {
			throw new Panic('Failed to retrieve history.');
		}

		foreach ($history as $entry) {
			if ($entry['hotfix'] === null) {
				$status['state'][$entry['channel']] = $this->binversion($entry['channel'], $entry['version']);
			}
		}

		// generate version history for upgrade
		foreach ($channels as $channel => &$timeline) {
			if (count($timeline['versions']) > 0) {
				end($timeline['versions']);
				$last_version = key($timeline['versions']);
				$this->processhistory($channel, $last_version, $status, $channels);
			}
		}

		// dry run?
		if ($dryrun) {
			// just return the step history
			return [$status['history'], 0];
		}

		$migrations = 0;
		if ( ! empty($status['history'])) {
			// execute the history
			foreach ($status['history'] as $entry) {
				// execute migration
				$this->processmigration($channels, $entry['channel'], $entry['version'], $entry['hotfix']);
				$migrations++;
			}
		}
		else { // no history
			$this->log(" No changes required.\n");
			return [$migrations, 0];
		}

		// operation complete
		return [$migrations, 0];

	}

	/**
	 * @return boolean
	 */
	static function supports($dbtype) {
		return in_array($dbtype, static::mysqlcompatibletypes());
	}

// ---- Private ---------------------------------------------------------------

	/**
	 * @return array mysql-compatible databases
	 */
	protected static function mysqlcompatibletypes() {
		return ['mariadb', 'mysql'];
	}

	/**
	 * Normalize configuration entry
	 *
	 * @return array
	 */
	protected function conf($confpath, $dependencies = []) {

		$conf = $this->confs->read($confpath);

		if (isset($conf['require'])) {
			throw new Panic("The paradox configuration $confpath must not contain a require key. Only the main paradox configuration should specify dependencies.");
		}

		return array_merge($conf, [ 'require' => $dependencies ]);
	}

	/**
	 * @return string
	 */
	protected function table() {
		return 'freia__timeline';
	}

	/**
	 * @return boolean
	 */
	protected function log($message) {
		// the writer function is a function that recieves just one string
		call_user_func($this->writer, $message);

		return true; // we return to allow use in shorthand conditionals
	}

	/**
	 * Formatting for step information in verbose output.
	 *
	 * @return string
	 */
	protected function step_format() {
		return ' %10s | %6s %s %s';
	}

	/**
	 * Step information for verbose output
	 *
	 * @return boolean
	 */
	protected function shout($op, $channel, $version, $note = null) {
		! $this->verbose or $this->log(sprintf($this->step_format()."\n", $op, $version, $channel, $note));
		return true; // allow use in shorthand conditionals
	}

	/**
	 * @return
	 */
	function dbh() {
		$dbtype = $this->dbtype;
		if (in_array($dbtype, static::mysqlcompatibletypes())) {
			return \fenrir\MysqlDatabase::instance($this->dbconf);
		}
		else { // unsuported
			throw new Panic("Database type not supported: $dbtype");
		}
	}

	/**
	 * Hook.
	 *
	 * @return array state
	 */
	protected function initialize_migration_state(array & $channelinfo, $channel, $version, $hotfix) {
		$self = $this;
		return [
			'writer' => function () use ($self) {
				$self->log(call_user_func_array('sprintf', func_get_args()));
			},
			'channelinfo' => & $channelinfo,
			'tables' => [],
			'identity' => [
				'channel' => $channel,
				'version' => $version,
				'hotfix'  => $hotfix,
			],
			'sql' => [
				'constants' => $this->constants,
				'default' => [
					'engine' => $this->default_db_engine(),
					'charset' => $this->default_db_charset(),
				],
			],
		];
	}

	/**
	 * ...
	 */
	protected function ensurehistorytable() {
		if (in_array($this->dbtype, static::mysqlcompatibletypes())) {
			if ( ! $this->has_history_table()) {
				$dbh = $this->dbh();
				// create history table
				\fenrir\SqlPdx::create_table
					(
						$dbh, $this->table(),
						'
							`id`          [primaryKey],
							`channel`     [title],
							`version`     [title],
							`hotfix`      [title] DEFAULT NULL,
							`timestamp`   [timestamp],
							`system`      [block],
							`description` [block],

							PRIMARY KEY (`id`)
						',
						$this->constants,
						$this->default_db_engine(),
						$this->default_db_charset()
					);
			}
		}
		else { // undefined state
			throw new Panic('Initialization of history table failed.');
		}
	}

	/**
	 * @return string
	 */
	protected function default_db_engine() {
		if (in_array($this->dbtype, static::mysqlcompatibletypes())) {
			return 'InnoDB';
		}
		else { // undefined state
			throw new Panic('Unable to retrieve DB engine.');
		}
	}

	/**
	 * @return string
	 */
	protected function default_db_charset() {
		return 'utf8';
	}

	/**
	 * @return int binary version
	 */
	protected function binversion($channel, $version) {

		// split version
		$v = explode('.', $version);

		if (count($v) !== 3) {
			throw new Panic("Invalid version: $channel $version");
		}

		// 2 digits for patch versions, 3 digits for fixes
		$binversion = intval($v[0]) * 100000 + intval($v[1]) * 1000 + intval($v[2]);

		if ($binversion == 0) {
			throw new Panic('The version of 0 is reserved.');
		}

		return $binversion;
	}

	/**
	 * Error report for situation where dependencies race against each other
	 * and a channels fall behind another in the requirement war.
	 */
	protected function dependency_race_error(array $status, $channel, $version) {
		// provide feedback on loop
		! $this->verbose or $this->log("\n");
		$this->log(" Race backtrace:\n");
		foreach ($status['active'] as $activeinfo) {
			$this->log("  - {$activeinfo['channel']} {$activeinfo['version']}\n");
		}
		$this->log("\n");

		throw new Panic("Target version breached by race condition on $channel $version");
	}

	/**
	 * @return array
	 */
	protected function channels() {

		// load configuration
		$pdx = $this->conf;

		// configure channels
		$channels = [];
		foreach ($pdx as $channelname => $channel) {

			$db = $this->dbh();

			foreach ($channel as $version => &$handler) {
				$handler['binversion'] = $this->binversion($channelname, $version);
			}

			uksort (
				$channel,
				function ($a, $b) use ($channel) {
					// split version
					$version1 = explode('.', $a);
					$version2 = explode('.', $b);

					if (count($version1) !== 3) {
						throw new Panic("Invalid version: $channel $a");
					}

					if (count($version2) !== 3) {
						throw new Panic("Invalid version: $channel $b");
					}

					if (intval($version1[0]) - intval($version2[0]) == 0) {
						if (intval($version1[1]) - intval($version2[1]) == 0) {
							return intval($version1[2]) - intval($version2[2]);
						}
						else { // un-equal versions
							return intval($version1[1]) - intval($version2[1]);
						}
					}
					else { // un-equal
						return intval($version1[0]) - intval($version2[0]);
					}
				}
			);

			// generate normalized version of channel info
			$channels[$channelname] = array
				(
					'current' => null,
					'db' => $db,
					'versions' => $channel,
				);
		}

		return $channels;
	}

	/**
	 * Performs migration steps and creates entry in timeline.
	 *
	 * To add steps add them under the configuration freia/paradox-steps and
	 * overwrite this class accordingly.
	 */
	protected function processmigration(array $channels, $channel, $version, $hotfix) {

		$this->log("\n");

		$stepformat = ' %15s %-9s %s%s';

		$steps = $this->stepconf;
		asort($steps);

		$chaninfo = $channels[$channel];
		$state = $this->initialize_migration_state($chaninfo, $channel, $version, $hotfix);

		// We save to the history first. If an error happens at least the
		// database history will show which step it happend on for future
		// reference; it also enabled us to do a clean install after an
		// exception instead of forcing a hard uninstall.
		$this->pushhistory($channel, $version, $hotfix, $chaninfo['versions'][$version]['description']);

		foreach ($steps as $step => $priority) {
			$this->log (
				sprintf (
					$stepformat,
					$step,
					$version,
					trim($channel),
					empty($hotfix) ? '' : ' / '.$hotfix
				)
			);

			$self = $this;
			$state['progress.writer'] = function ($done, $total) use ($self, $stepformat, $step, $version, $channel, $hotfix)
				{
					if ($self->context->php_sapi_name() === 'cli') {
						$self->log("\r");
						$self->log(str_repeat(' ', 80));
						$self->log("\r");

						$self->log (
							sprintf (
								$stepformat,
								$step,
								$version,
								trim($channel),
								(empty($hotfix) ? '' : ' / '.$hotfix).' - '.(number_format(round($done * 100 / $total, 2), 2)).'%'
							)
						);
					}
					else { // non-CLI context
						// do nothing
					}

				};

			$method = $this->steplogic($step);
			$method->process($chaninfo['versions'][$version], $state);

			if ($this->context->php_sapi_name() === 'cli') {
				$this->log("\r");
				$this->log(str_repeat(' ', 80));
				$this->log("\r");
			}
			else { // standard end of line
				$this->log("\n");
			}
		}

		if ( ! isset($chaninfo['versions'][$version]['description'])) {
			throw new Panic("Missing description for {$channel} {$version}");
		}

		if ($this->context->php_sapi_name() === 'cli') {
			$this->log("\r");
			$this->log(str_repeat(' ', 80));
			$this->log("\r");
		}

		$this->log (
			sprintf (
				$stepformat,
				'- complete -',
				$version,
				trim($channel),
				empty($hotfix) ? '' : '/ '.$hotfix
			)
		);

		if ($this->context->php_sapi_name() !== 'cli') {
			$this->log("\n");
		}
	}

	/**
	 * @return array
	 */
	protected function processhistory($channel, $target_version, array & $status, array & $channels) {

		$this->shout('fulfilling', $channel, $target_version);

		if ( ! isset($channels[$channel])) {
			throw new Panic("Required channel $channel not available.");
		}

		if ( ! isset($channels[$channel]['versions'][$target_version])) {
			throw new Panic("Required version $target_version in channel $channel not available.");
		}

		// recursion detection
		if (in_array($channel, array_column($status['active'], 'channel'))) {
			// provide feedback on loop
			! $this->verbose or $this->log("\n");
			$this->log(" Loop backtrace:\n");
			foreach ($status['active'] as $activeinfo) {
				$this->log("  - {$activeinfo['channel']} {$activeinfo['version']}\n");
			}
			$this->log("\n");

			throw new Panic("Recursive dependency detected on $channel $target_version");
		}

		$timeline = $channels[$channel];

		if ( ! isset($status['state'][$channel])) {
			$status['state'][$channel] = 0;
		}

		$status['active'][] = [ 'channel' => $channel, 'version' => $target_version ];
		$targetver = $this->binversion($channel, $target_version);

		// verify state
		if ($targetver < $status['state'][$channel]) {
			return; // version already satisfied in the timeline; skipping...
		}

		// process versions
		foreach ($timeline['versions'] as $litversion => $version) {
			if ($version['binversion'] <= $status['state'][$channel]) {
				continue; // version already processed; skipping...
			}

			if (isset($version['require']) && ! empty($version['require'])) {
				foreach ($version['require'] as $required_channel => $required_version) {
					if (isset($status['state'][$required_channel])) {
						// check if version is satisfied
						$versionbin = $this->binversion($required_channel, $required_version);
						if ($status['state'][$required_channel] == $versionbin) {
							continue; // dependency satisfied
						}
						else if ($status['state'][$required_channel] > $versionbin) {

							// the required version has been passed; since the
							// state of the channel may change from even the
							// smallest of changes; versions being passed is
							// not acceptable

							$this->dependency_race_error
								(
									// the scene
									$status,
									// the victim
									$channel,
									$target_version
								);
						}

						// else: version is lower, pass through
					}

					$this->shout('require', $required_channel, $required_version, '>> '.$channel.' '.$litversion);
					$this->processhistory($required_channel, $required_version, $status, $channels);
				}
			}

			// requirements have been met
			$status['history'][] = array
				(
					'hotfix'  => null,
					'channel' => $channel,
					'version' => $litversion,
				);

			// update state
			$status['state'][$channel] = $version['binversion'];
			$this->shout('completed', $channel, $litversion);

			// the channel is at a new version, but before continuing to the
			// next version we need to check if any channel requirements have
			// been satisfied in the process, if they have that channel needs
			// to be bumped to this version; else we enter an unnecesary loop
			// generated by processing order--we use the checklist generated
			// at the start of the process for this purpose
			if (isset($status['checklist'][$channel]) && isset($status['checklist'][$channel][$litversion])) {
				foreach ($status['checklist'][$channel][$litversion] as $checkpoint) {

					// we skip over actively processed requirements

					$skip = false;
					foreach ($status['active'] as $active)
					{
						$active_version = $this->binversion($active['channel'], $active['version']);
						$checkpoint_version = $this->binversion($checkpoint['channel'], $checkpoint['version']);
						// we test with >= on the version because we know that
						// if a channel did require that specific version then
						// they would have initiated the process, thereby
						// rendering it impossible to cause conflict, ie.
						// requirement should have been satisfied already
						if ($active['channel'] == $checkpoint['channel'] && $active_version >= $checkpoint_version) {
							$skip = true;
							break;
						}
					}

					if ($skip) {
						$this->shout('pass:point', $checkpoint['channel'], $checkpoint['version'], '-- '.$channel.' '.$litversion);
						continue; // requested version already being processed
					}

					// are all requirements of given checkpoint complete? if
					// the checkpoint starts resolving requirements of it's own
					// it's possible for it to indirectly loop back

					$cp = $channels[$checkpoint['channel']]['versions'][$checkpoint['version']];
					$skip_checkpoint = false;
					if (isset($cp['require']) && ! empty($cp['require'])) {
						foreach ($cp['require'] as $required_channel => $required_version) {
							if (isset($status['state'][$required_channel])) {
								// check if version is satisfied
								$versionbin = $this->binversion($required_channel, $required_version);
								if ($status['state'][$required_channel] == $versionbin) {
									continue; // dependency satisfied
								}
								else if ($status['state'][$required_channel] > $versionbin) {

									// the required version has been passed; since the state
									// of the channel may change from even the smallest of
									// changes; versions being passed is not acceptable

									$this->dependency_race_error
										(
											// the scene
											$status,
											// the victim
											$checkpoint['channel'],
											$checkpoint['version']
										);
								}

								// else: version is lower, pass through
							}

							$skip_checkpoint = true;
						}
					}

					if ($skip_checkpoint) {
						$this->shout('hold:point', $checkpoint['channel'], $checkpoint['version'], '-- '.$channel.' '.$litversion);
						continue; // checkpoint still has unfilled requirements
					}

					$this->shout('checklist', $checkpoint['channel'], $checkpoint['version'], '<< '.$channel.' '.$litversion);
					$this->processhistory($checkpoint['channel'], $checkpoint['version'], $status, $channels);
				}
			}

			// has target version been satisfied?
			if ($targetver === $version['binversion']) {
				break; // completed required version
			}
		}

		// remove channel from active information
		$new_active = [];
		foreach ($status['active'] as $active) {
			if ($active['channel'] !== $channel) {
				$new_active[] = $active;
			}
		}

		$status['active'] = $new_active;
	}

	/**
	 * @return array
	 */
	protected function generate_checklist($channels) {

		$checklist = [];

		foreach ($channels as $channelname => $channelinfo) {
			foreach ($channelinfo['versions'] as $version => $handlers) {
				if (isset($handlers['require'])) {
					foreach ($handlers['require'] as $reqchan => $reqver) {
						isset($checklist[$reqchan]) or $checklist[$reqchan] = [];
						isset($checklist[$reqchan][$reqver]) or $checklist[$reqchan][$reqver] = [];

						// save a copy of what channels and versions depend on
						// the specific required version so we can reference it
						// back easily in processing and satisfy those
						// requirements to avoid process order induced loops
						$checklist[$reqchan][$reqver][] = array
							(
								'channel' => $channelname,
								'version' => $version,
							);
					}
				}
			}
		}

		return $checklist;
	}

	/**
	 * Loads tables from configuration
	 */
	protected function rm__load_tables(array &$config, array $handlers) {
		if (isset($handlers['configure'])) {
			$conf = $handlers['configure'];
			if (is_array($conf)) {
				if (isset($conf['tables'])) {
					foreach ($conf['tables'] as $table) {
						$config['tables'][] = $table;
					}
				}
			}
			else { // callback
				$config = $conf($config);
			}
		}
	}

	/**
	 * ...
	 */
	protected function pushhistory($channel, $version, $hotfix, $description) {
		$this->ensurehistorytable();

		// compute system version
		$versioninfo = $this->versioninfo();
		$system = \hlin\Arr::join(', ', $versioninfo, function ($component, $version) {
			return "$component $version";
		});

		$this->add_history_entry
			(
				[
					'channel' => $channel,
					'version' => $version,
					'hotfix'  => $hotfix,
					'system'  => $system,
					'description' => $description,
				]
			);
	}

	/**
	 * ...
	 */
	protected function add_history_entry($entry) {
		if (in_array($this->dbtype, static::mysqlcompatibletypes())) {
			\fenrir\SqlPdx::insert($this->dbh(), $this->table(), $entry);
		}
		else { // undefined state
			throw new Panic('Unable to add DB entry');
		}
	}

	/**
	 * @var array store of instantiated methods
	 */
	protected $_method_cache = [];

	/**
	 * The method returns a step "method class" which is a class that has
	 * an process method with the signature (array $handlers, array & $state)
	 *
	 * @return mixed step method instance
	 */
	protected function steplogic($step) {
		if ( ! isset($this->_method_cache[$step])) {
			$dbh = $this->dbh();
			$class = \hlin\PHP::pnn(call_user_func($this->stepclass, $step));
			$this->_method_cache[$step] = $class::instance($dbh);
		}
		return $this->_method_cache[$step];
	}

	/**
	 * @return boolean
	 */
	protected function has_history_table() {
		$dbh = $this->dbh();
		$timeline_table = $this->table();

		$tables = $dbh->prepare("SHOW TABLES LIKE '$timeline_table'")
			->execute()
			->fetch_all();

		return ! empty($tables);
	}

} # class
