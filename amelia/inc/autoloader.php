<?php

spl_autoload_register(function ($class) {
	$namespace = 'P2P\Amelia';

	if (strpos($class, $namespace) !== 0) {
		return;
	}

	$class = str_replace($namespace, '', $class);
	$class = str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';

	$path = P2P_AMELIA_PATH . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . $class;

	if (file_exists($path)) {
		require_once($path);
	}
});
