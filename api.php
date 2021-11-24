<?php

if (!defined('START_UP_BASE_PATH')) {
	define('START_UP_BASE_PATH', __DIR__);
}

if (!defined('START_UP_FILES_PATH')) {
	define('START_UP_FILES_PATH', __DIR__ . '/files');
}

require_once START_UP_BASE_PATH . '/controllers/api.php';


$api = new \StartUp\Api();
$uri = isset($_SERVER['REQUEST_URI']) ? explode('/', trim($_SERVER['REQUEST_URI'], '/')) : null;

if (is_null($uri) && isset($_SERVER['argv']) && is_array($_SERVER['argv'])) {
	$uri = $_SERVER['argv'];
}

array_shift($uri);

if (!$uri) {
	die();
}

$method = reset($uri);

if (method_exists($api, $method)) {
	array_shift($uri);
	$api->$method(... $uri);
} else {
	$api->index(... $uri);
}


