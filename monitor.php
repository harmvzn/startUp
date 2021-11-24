<?php

/*
 * http://localhost/start_up/monitor
 */
namespace StartUp;

if (!defined('START_UP_BASE_PATH')) {
	define('START_UP_BASE_PATH', __DIR__);
}

require_once START_UP_BASE_PATH . '/controllers/monitor_controller.php';

$monitor = new \StartUp\Monitor_controller();
$monitor->show();
