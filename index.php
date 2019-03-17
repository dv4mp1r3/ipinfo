<?php

define('MMVC_DEBUG', true);
define('MMVC_PROJECT_NAMESPACE', 'ipinfo');

require_once 'vendor/autoload.php';
$config = require_once 'config.php';
$app = new mmvc\core\Application($config);
$app->run();