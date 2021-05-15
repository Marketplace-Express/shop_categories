<?php

/**
 * App config file
 */

defined('BASE_PATH') || define('BASE_PATH', dirname(dirname(__DIR__)));
defined('APP_PATH') || define('APP_PATH', BASE_PATH . '/app');

return new \Phalcon\Config([
    'database' => [
        'adapter' => 'Mysql',
        'host' => $_ENV['MYSQL_HOST'],
        'port' => $_ENV['MYSQL_PORT'],
        'username' => $_ENV['MYSQL_USER'],
        'password' => $_ENV['MYSQL_PASSWORD'],
        'dbname' => $_ENV['MYSQL_DATABASE'],
        'charset' => $_ENV['MYSQL_CHARSET']
    ],
    'mongodb' => [
        'host' => $_ENV['MONGODB_HOST'],
        'port' => $_ENV['MONGODB_PORT'],
        'username' => $_ENV['MYSQL_USER'],
        'password' => $_ENV['MONGODB_PASSWORD'],
        'dbname' => $_ENV['MONGODB_DATABASE']
    ],
    'cache' => [
        'categories_cache' => [
            'host' => $_ENV['REDIS_HOST'],
            'port' => $_ENV['REDIS_PORT'],
            'persistent' => $_ENV['CATEGORIES_CACHE_PERSISTENT'],
            'database' => $_ENV['CATEGORIES_CACHE_DB'],
            'ttl' => $_ENV['CATEGORIES_CACHE_TTL'],
            'auth' => $_ENV['REDIS_AUTH']
        ],
        'attributes_cache' => [
            'host' => $_ENV['REDIS_HOST'],
            'port' => $_ENV['REDIS_PORT'],
            'persistent' => $_ENV['ATTRIBUTES_CACHE_PERSISTENT'],
            'database' => $_ENV['ATTRIBUTES_CACHE_DB'],
            'ttl' => $_ENV['ATTRIBUTES_CACHE_TTL'],
            'auth' => $_ENV['REDIS_AUTH']
        ]
    ],
    'rabbitmq' => [
        'host' => $_ENV['RABBITMQ_HOST'],
        'port' => $_ENV['RABBITMQ_PORT'],
        'username' => $_ENV['RABBITMQ_USER'],
        'password' => $_ENV['RABBITMQ_PASSWORD'],
        'sync_queue' => [
            'queue_name' => $_ENV['RABBITMQ_SYNC_QUEUE_NAME'],
            'message_ttl' => $_ENV['RABBITMQ_SYNC_MESSAGE_TTL']
        ],
        'async_queue' => [
            'queue_name' => $_ENV['RABBITMQ_ASYNC_QUEUE_NAME'],
            'message_ttl' => $_ENV['RABBITMQ_ASYNC_MESSAGE_TTL']
        ]
    ],
    'application' => [
        'modelsDir' => APP_PATH . '/common/models/',
        'controllersDir' => APP_PATH . '/modules/api/controllers/',
        'migrationsDir' => APP_PATH . '/migrations/',
        'logsDir' => APP_PATH . '/logs/',
        'cacheDir' => APP_PATH . '/cache/',
        'stopWords' => APP_PATH . '/misc/stop_words.json',
        'api' => [
            'base_uri' => $_ENV['API_HOST'],
            'timeout' => $_ENV['API_TIMEOUT']
        ],
        'graphql' => [
            'maxQueryDepth' => $_ENV['GRAPHQL_MAX_QUERY_DEPTH']
        ]
    ],
    'printNewLine' => true
]);
