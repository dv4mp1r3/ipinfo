<?php

use mmvc\core\Application;

define('GEOIP_CITY_PATH', '');
define('GEOIP_ASN_PATH', '');

return [
    Application::CONFIG_KEY_DB =>
        [
            Application::CONFIG_PARAM_DB_DRIVER => mmvc\models\data\RDBHelper::DB_TYPE_MYSQL,
            Application::CONFIG_PARAM_DB_USERNAME => '',
            Application::CONFIG_PARAM_DB_PASSWORD => '',
            Application::CONFIG_PARAM_DB_HOST => '',
            Application::CONFIG_PARAM_DB_SCHEMA => '',
        ],
    Application::CONFIG_KEY_USERS => [
        '' =>
            [
                'username' => '',
                'password' => '',
                'user_hash' => '',
            ],
    ],
    Application::CONFIG_KEY_LOGPATH => dirname(__FILE__) . DIRECTORY_SEPARATOR . 'log' . DIRECTORY_SEPARATOR . 'main.log',
    Application::CONFIG_KEY_TIMEZONE => 'Etc/GMT-3',
    Application::CONFIG_KEY_ROUTE => mmvc\core\Router::ROUTE_TYPE_DEFAULT,
    Application::CONFIG_KEY_DEFAULT_ACTION => [
        Application::CONFIG_PARAM_DEFAULT_CONTROLLER => 'guest',
        Application::CONFIG_KEY_DEFAULT_ACTION => 'info'
    ],

];