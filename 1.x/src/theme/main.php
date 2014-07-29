<?php namespace app\theme;

/**
 * @return string
 */
function render($_file, $_vars) {
	extract($_vars);
	unset($_vars);
	ob_start();
	include $_file;
	return ob_get_clean();
}

/**
 * @return string
 */
function main($view, $data = null) {

	$themepath = realpath(__DIR__);

	// interpret view
	if (($ptr = stripos($view, ':')) === false) {
		throw new \Exception('[Theme] Views are required to have a domain');
	}

	$domain = substr($view, 0, $ptr);
	$idx = substr($view, $ptr + 1);

	// security check
	if ( ! preg_match('/[a-zA-Z0-9-]+/', $domain)) {
		throw new \Exception("[Theme] Security violation on view domain: $domain");
	}

	// load domain
	$domains = include "$themepath/modules/mapping.php"
	$conf = $domains[$domain];

	// check configuration
	if ( ! isset($conf[$idx])) {
		throw new \Exception("[Theme] Missing view: $view");
	}

	$viewfile = "$domain/{$conf[$idx]}";

	if ($domain != 'public') {
		$viewfile = "$themepath/modules/$viewfile.php";
	}
	else { // public
		$viewfile = "$themepath/$viewfile";
	}

	if ( ! file_exists($viewfile)) {
		throw new \Exception("[Theme] Failed request for $view, missing required file: $viewfile");
	}

	if ($data === null) {
		$data = [];
	}

	return render($viewfile, $data);
}
