<?php
/**
 * This file is called in php.ini look in php.ini search for "auto_prepend_file"
 *
 */
$beforeInitHook = realpath(__DIR__.'/beforeInitHook.php');
if ($beforeInitHook) {
	require_once $beforeInitHook;
}
unset($beforeInitHook);

$inifile = realpath(__DIR__.'/startUp.ini');
if ($inifile) {
	$ini = parse_ini_file($inifile);
	define('START_UP_BASE_PATH', $ini['basePath']);

	if (!empty($ini['filesPath'])) {
		define('START_UP_FILES_PATH', $ini['filesPath']);
	} else {
		define('START_UP_FILES_PATH', START_UP_BASE_PATH . '/files');
	}

	if (!empty($ini['outputDepth'])) {
		define('OUTPUT_DEPTH', $ini['outputDepth']);
	} else {
		define('OUTPUT_DEPTH', 3);
	}

	if (!isset($_GET['disable_localhost_debug'])) {
		require_once START_UP_BASE_PATH . '/ini.php';
		new StartUp\start_up_ini;
	}
	unset($ini);
}
unset($inifile);
