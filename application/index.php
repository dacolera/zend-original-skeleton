<?php

$cookie = session_get_cookie_params();
session_set_cookie_params($cookie['lifetime'], $cookie['path'], $cookie['domain'], $cookie['secure'], true);

set_include_path(
	implode(PATH_SEPARATOR, array(
		realpath(dirname(__FILE__) . '/library'),
		realpath(dirname(__FILE__) . '/library/Pear'),
		get_include_path(),
	))
);

defined('APPLICATION') ||
	define('APPLICATION', str_replace('application', '', realpath(dirname(__FILE__))));

defined('APPLICATION_ROOT') ||
	define('APPLICATION_ROOT', realpath(dirname(__FILE__)));

defined('APPLICATION_PATH') ||
	define('APPLICATION_PATH', sprintf('%s%smodules%s%s', APPLICATION_ROOT, DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, (getenv('MODULE') ? getenv('MODULE') : 'site')));
	
defined('APPLICATION_ENV') ||
	define('APPLICATION_ENV', (getenv('ENVIRONMENT') ? getenv('ENVIRONMENT') : 'production'));

/** Zend_Application */
require_once 'Zend/Application.php';

$application = new Zend_Application(
	APPLICATION_ENV,
	APPLICATION_ROOT . '/configs/application.ini'
);

$application->bootstrap();
$application->run();
