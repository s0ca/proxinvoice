<?php

//	to be removed for production
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('error_reporting', E_ALL);
//	============================

define('ROOT', dirname(__FILE__) . '/');
define('LIBS', ROOT . '/libs/');

ob_start();
if (file_exists('config.php')) {
	require ROOT . 'diff.php';
} else {
	require ROOT . 'admin/install.php';
}
$content = ob_get_clean();

require 'template/body.php';

