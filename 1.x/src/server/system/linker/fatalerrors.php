<?php namespace app\main;

/**
 * ...
 */
function fatalerrors($logspath) {

	if ($logspath === false || empty($logspath)) {
		log_error('[ERROR] Bad logging path! No file logging will be available.');
	}

	register_shutdown_function(function () use ($logspath) {
		$death = error_get_last();
		if ($death !== null) {
			$timestamp = date('[Y-m-d|H:i:s]');
			// record in fatal errors log
			$fatalerror = "$logspath/fatalerrors.log";
			file_exists($fatalerror) or file_put_contents($fatalerror, '');
			error_log("$timestamp {$death['message']} @ {$death['file']} [{$death['type']}]\n", 3, $fatalerror);
			// record in summary log
			$summary = "$logspath/summary.log";
			file_exists($summary) or file_put_contents($summary, '');
			error_log("$timestamp {$death['message']} @ {$death['file']} [{$death['type']}]\n", 3, $summary);
		}
	});
}
