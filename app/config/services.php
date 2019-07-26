<?php

use Phalcon\Mvc\Model\Metadata\Memory as MetaDataAdapter;
use Shop_categories\Services\User\UserService;

/**
 * Shared configuration service
 */
$di->setShared('config', function () {
    return include APP_PATH . "/config/config.php";
});

/**
 * Profiler service
 */
$di->setShared('profiler', function () {
    return new Phalcon\Db\Profiler();
});

/**
 * Register MySQL Database connection
 */
$di->setShared('db', function () {
    $config = $this->getConfig();

    $class = 'Phalcon\Db\Adapter\Pdo\\' . $config->database->adapter;
    $params = [
        'host'     => $config->database->host,
        'username' => $config->database->username,
        'password' => $config->database->password,
        'dbname'   => $config->database->dbname,
        'charset'  => $config->database->charset
    ];

    if ($config->database->adapter == 'Postgresql') {
        unset($params['charset']);
    }

    /**
     * @var \Phalcon\Db\Adapter\Pdo $connection
     */
    $connection = new $class($params);

    /**
     * @var \Phalcon\Db\Profiler $profiler
     */
    $profiler = $this->getProfiler();
    $eventsManager = new \Phalcon\Events\Manager();
    $eventsManager->attach('db', function ($event, $connection) use ($profiler, $config) {
        /**
         * @var \Phalcon\Events\Event $event
         * @var \Phalcon\Db\Adapter\Pdo $connection
         */
        if ($event->getType() == 'beforeQuery') {
            $profiler->startProfile($connection->getSQLStatement());
        }

        if ($event->getType() == 'afterQuery') {
            $profiler->stopProfile();

            if (!file_exists($config->application->logsDir . 'db.log')) {
                touch($config->application->logsDir . 'db.log');
            }

            // Log last SQL statement
            \Phalcon\Logger\Factory::load([
                'name' => $config->application->logsDir . 'db.log',
                'adapter' => 'file'
            ])->info($profiler->getLastProfile()->getSqlStatement());
        }
    });
    $connection->setEventsManager($eventsManager);

    return $connection;
});

/**
 * Register Mongo Database connection
 */
$di->setShared('mongo', function(){
    $config = $this->getConfig();
    $connectionString = "mongodb://";
    if (!empty($config->mongodb->username) && !empty($config->mongodb->password)) {
        $connectionString  .= $config->mongodb->username.":".$config->mongodb->password."@";
    }
    $connectionString .= $config->mongodb->host.":".$config->mongodb->port;
    $mongo = new \Phalcon\Db\Adapter\MongoDB\Client($connectionString);
    return $mongo->selectDatabase($config->mongodb->dbname);
});

$di->setShared(
    'collectionManager',
    function () {
        return new \Phalcon\Mvc\Collection\Manager();
    }
);

/**
 * If the configuration specify the use of metadata adapter use it or use memory otherwise
 */
$di->setShared('modelsMetadata', function () {
    return new MetaDataAdapter();
});

/**
 * Register Redis as a service
 */
$di->setShared('cache', function(int $database = 0) {
    $config = $this->getConfig()->cache;
    $redisInstance = new \Shop_categories\Redis\Connector();
    $redisInstance->connect(
        $config->category_cache->host,
        $config->category_cache->port,
        $database,
        $config->category_cache->auth
    );
    return ['adapter' => $redisInstance, 'instance' => $redisInstance->redis];
});

/**
 * Register cache service
 */
$di->setShared('categoryCache', function () {
    $config = $this->getConfig()->cache->category_cache;
    return $this->getCache($config->database)['instance'];
});

$di->setShared('categoryCacheIndex', function () {
    $config = $this->getConfig()->cache->category_cache;
    return new \Ehann\RediSearch\Index($this->get('cache', [$config->database])['adapter'],
        \Shop_categories\Enums\CacheIndexesEnum::CATEGORY_INDEX_NAME);
});

$di->setShared('categoryCacheSuggest', function() {
    $config = $this->getConfig()->cache->category_cache;
    return new \Ehann\RediSearch\Suggestion($this->getCache($config->database, true)['adapter'],
        \Shop_categories\Enums\CacheIndexesEnum::CATEGORY_INDEX_NAME);
});

$di->setShared('attributesCache', function () {
    $config = $this->getConfig()->cache->attributes_cache;
    return $this->getCache($config->database)['instance'];
});

$di->setShared('logger', function() {
    return new \Shop_categories\Logger\ApplicationLogger();
});

/** RabbitMQ service */
$di->setShared('queue', function () {
    $config = $this->getConfig();
    $connection = new \PhpAmqpLib\Connection\AMQPStreamConnection(
        $config->rabbitmq->host,
        $config->rabbitmq->port,
        $config->rabbitmq->username,
        $config->rabbitmq->password
    );
    $channel = $connection->channel();
    $channel->queue_declare($config->rabbitmq->sync_queue->queue_name,
        false, false, false, false, false,
        new \PhpAmqpLib\Wire\AMQPTable(['x-message-ttl' => $config->rabbitmq->sync_queue->message_ttl])
    );
    $channel->queue_declare($config->rabbitmq->async_queue->queue_name,
        false, false, false, false, false,
        new \PhpAmqpLib\Wire\AMQPTable(['x-message-ttl' => $config->rabbitmq->async_queue->message_ttl])
    );
    return $channel;
});

$di->setShared(
    'userService',
    UserService::class
);