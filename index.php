<?php


use mmvc\core\Router;

define('MMVC_DEBUG', true);
define('MMVC_ROOT_DIR', dirname(__FILE__));
define('MMVC_VENDOR_NAMESPACE', 'mmvc');
define('MMVC_PROJECT_NAMESPACE', 'ipinfo');

require_once 'vendor/autoload.php';
require_once 'config.php';

if (!defined('DEBUG') || DEBUG === false) {
    set_error_handler('mmvc\\core\\ExceptionHandler::doError');
}

date_default_timezone_set($config['timezone']);

$router = null;

if (php_sapi_name() === 'cli') {
    set_exception_handler('mmvc\\core\\ExceptionHandler::doCliAppException');
    $router = new Router(Router::ROUTE_TYPE_CLI);
} else {
    set_exception_handler('mmvc\\core\\ExceptionHandler::doWebAppException');
    session_start();
    $router = new Router($config['route']);
}

$router->route();
