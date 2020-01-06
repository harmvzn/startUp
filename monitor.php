<?php
/*
 * http://localhost/start_up/monitor
 */
namespace StartUp;

require_once START_UP_BASE_PATH . '/controllers/monitor_controller.php';

$monitor = new \StartUp\Monitor_controller();
$monitor->show();
