<?php namespace app\main;

/**
 * @return \hlin\archetype\Authorizer
 */
function authorizer($context, $mysql) {

	$conf = realpath(__DIR__.'/conf');

	$whitelist = include "$conf/whitelist.php";
	$blacklist = include "$conf/blacklist.php";
	$aliaslist = include "$conf/aliaslist.php";

	return \hlin\Auth::instance($whitelist, $blacklist, $aliaslist);
}
