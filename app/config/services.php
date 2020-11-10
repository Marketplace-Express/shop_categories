<?php

use app\common\enums\CacheIndexesEnum;
use app\common\logger\ApplicationLogger;
use app\common\redis\Connector;
use app\common\services\user\UserService;
use app\common\utils\AMQPHandler;
use Ehann\RediSearch\Index;
use Ehann\RediSearch\Suggestion;
use Phalcon\Db\Adapter\MongoDB\Client as MongoClient;
use Phalcon\Db\Profiler;
use Phalcon\Events\Event;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Logger\Factory;
use Phalcon\Mvc\Collection\Manager as CollectionManager;
use Phalcon\Mvc\Model\Metadata\Memory as MetaDataAdapter;
use Phalcon\Mvc\Model\MetaData\Strategy\Annotations;
use PhpAmqpLib\Connection\AMQPStreamConnection;


/**
 * Shared configuration service
 */
$di->setShared('config', function () {
    return require(APP_PATH . '/config/config.php');
});

/**
 * Profiler service
 */
$di->setShared('profiler', Profiler::class);

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
     * @var Profiler $profiler
     */
    $profiler = $this->getProfiler();
    $eventsManager = new EventsManager();
    $eventsManager->attach('db', function ($event, $connection) use ($profiler, $config) {
        /**
         * @var Event $event
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

            $lastProfile = $profiler->getLastProfile();

            // Log last SQL statement
            Factory::load([
                'name' => $config->application->logsDir . 'db.log',
                'adapter' => 'file'
            ])->info(
                $lastProfile->getSqlStatement()
                ."\n"
                .json_encode($connection->getSQLVariables())
            );
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
    $mongo = new MongoClient($connectionString);
    return $mongo->selectDatabase($config->mongodb->dbname);
});

/**
 * Collection Manager
 */
$di->setShared('collectionManager', CollectionManager::class);

/**
 * If the configuration specify the use of metadata adapter use it or use memory otherwise
 */
$di->setShared('modelsMetadata', function () {
    $metadata = new MetaDataAdapter([
        'lifetime' => 1
    ]);
    $metadata->setStrategy(new Annotations());
    return $metadata;
});

/**
 * Register Redis as a service
 */
$di->set('cache', function(int $database = 0) {
    $config = $this->getConfig()->cache;
    $redisInstance = new Connector();
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
    return new Index($this->get('cache', [$config->database])['adapter'],
        CacheIndexesEnum::CATEGORY_INDEX_NAME);
});

$di->setShared('categoryCacheSuggest', function() {
    $config = $this->getConfig()->cache->category_cache;
    return new Suggestion($this->getCache($config->database, true)['adapter'],
        CacheIndexesEnum::CATEGORY_INDEX_NAME);
});

$di->setShared('attributesCache', function () {
    $config = $this->getConfig()->cache->attributes_cache;
    return $this->getCache($config->database)['instance'];
});

$di->setShared('logger', function() {
    return new ApplicationLogger();
});

/** RabbitMQ service */
$di->setShared('amqp', function () {
    $config = $this->getConfig();
    $connection = new AMQPStreamConnection(
        $config->rabbitmq->host,
        $config->rabbitmq->port,
        $config->rabbitmq->username,
        $config->rabbitmq->password
    );
    $channel = $connection->channel();

    return new AMQPHandler($channel, $config);
});

/**
 * UserService should be shared among application
 */
$di->setShared('userService', UserService::class);

/**
 * AppServices
 */
$di->set('appServices', function($serviceName) {
    $services = [
        'categoryService' => 'app\common\services\CategoryService',
        'attributeService' => 'app\common\services\AttributesService',
        'searchService' => 'app\common\services\SearchService'
    ];

    if (!array_key_exists($serviceName, $services)) {
        throw new Exception('DI: Service not found', 500);
    }

    return new $services[$serviceName];
});
