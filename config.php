<?php

use mmvc\core\Application;

return [
    Application::CONFIG_KEY_DB =>
    [
        Application::CONFIG_PARAM_DB_DRIVER => mmvc\models\data\RDBHelper::DB_TYPE_MYSQL,
        Application::CONFIG_PARAM_DB_USERNAME => 'root',
        Application::CONFIG_PARAM_DB_PASSWORD => '',
        Application::CONFIG_PARAM_DB_HOST => 'localhost',
        Application::CONFIG_PARAM_DB_SCHEMA => 'mmvc_test',
    ],
    Application::CONFIG_KEY_USERS => [
        'admin' =>
        [
            'username' => 'admin',
            'password' => '123',
            'user_hash' => '24wejdslkfjsdfh2k3h5qwd',
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
