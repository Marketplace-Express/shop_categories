<?php

/**
 * App config file
 */

defined('BASE_PATH') || define('BASE_PATH', dirname(dirname(__DIR__)));
defined('APP_PATH') || define('APP_PATH', BASE_PATH . '/app');

return new \Phalcon\Config([
    'database' => [
        'adapter' => 'Mysql',
        'host' => '172.19.0.1',
        'port' => 3306,
        'username' => 'phalcon',
        'password' => 'secret',
        'dbname' => 'shop_categories',
        'charset' => 'utf8'
    ],
    'mongodb' => [
        'host' => '172.19.0.1',
        'port' => 27017,
        'username' => null,
        'password' => null,
        'dbname' => 'shop_categories'
    ],
    'cache' => [
        'category_cache' => [
            'host' => '172.19.0.1',
            'port' => 6379,
            'persistent' => true,
            'database' => 4,
            'ttl' => -1,
            'auth' => null
        ],
        'attributes_cache' => [
            'host' => '172.19.0.1',
            'port' => 6379,
            'persistent' => true,
            'database' => 5,
            'ttl' => -1,
            'auth' => null
        ]
    ],
    'rabbitmq' => [
        'host' => '172.19.0.1',
        'port' => 5672,
        'username' => 'guest',
        'password' => 'guest',
        'sync_queue' => [
            'queue_name' => 'categories_sync',
            'message_ttl' => 10000
        ],
        'async_queue' => [
            'queue_name' => 'categories_async',
            'message_ttl' => 10000
        ]
    ],
    'application' => [
        'modelsDir' => 'app/common/models/',
        'controllersDir' => 'app/modules/api/controllers/',
        'migrationsDir' => 'app/migrations/',
        'logsDir' => 'app/logs/',
        'cacheDir' => 'app/cache/',
        'stopWords' => 'app/stop_words.json',
        'token' => [
            'saltKey' => 'abc@123456789012',
            'allowedAlg' => 'HS512'
        ]
    ],
    'printNewLine' => true
]);
