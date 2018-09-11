<?php

require_once HARM_START_UP_BASE_PATH . '/controllers/api.php';

$api = new \Harm\Api();
$uri = isset($_SERVER['REQUEST_URI']) ? explode('/', trim($_SERVER['REQUEST_URI'], '/')) : null;

$default_array = array('a', 'b', 'c', 'd', 'e', 'f', 'g');

if (is_null($uri) && isset($_SERVER['argv']) && is_array($_SERVER['argv'])) {
	$uri = $_SERVER['argv'];
	array_shift($uri);
	if (!$uri) {
		die();
	}
	foreach ($default_array as $index => $default) {
		$$default = isset($uri[$index]) ? $uri[$index] : '';
	}
} else {
	if (!$uri) {
		die();
	}
	foreach ($default_array as $index => $default) {
		$$default = isset($uri[$index + 2]) ? $uri[$index + 2] : '';
	}
}

if (method_exists($api, $a)) {
	$api->$a($b, $c, $d, $e, $f, $g);
} else {
	$api->index($a, $b, $c, $d, $e, $f, $g);
}


