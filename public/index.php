<?php
// Start sessions
session_start();
// Turn on error reporting
ini_set('display_errors', true);
ini_set('error_reporting', E_ALL);
// Define our base path
if (defined('BASEPATH') === false) {
	define('BASEPATH', dirname(__FILE__));
}
// Define our library path
if (defined('LIBRARYPATH') === false) {
	define('LIBRARYPATH', BASEPATH.'/../library');
}
// Define our application path
if (defined('APPLICATIONPATH') === false) {
	define('APPLICATIONPATH', BASEPATH.'/../application');
}
// Load our Phwork library
require_once(LIBRARYPATH.'/Phwork.php');
// Register our autoloader
spl_autoload_register(array(Phwork::getInstance(), 'AutoLoadClass'));
// Register our error handler
set_error_handler(array(Phwork::getInstance(), 'RunError'));
// Register our exception handler
set_exception_handler(array(Phwork::getInstance(), 'RunError'));
// Check to see if we
// need to force SSL
if ((Phwork::Config('SystemSettings', 'ForceSSL') === true) && ($_SERVER['SERVER_PORT'] != 443)) {
	// Do the redirect
	header("Location:  https://{$_SERVER['SERVER_NAME']}{$_SERVER['REQUEST_URI']}");
}
// Initialize Phwork
Phwork::getInstance()->run();
