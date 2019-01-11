<?php

use Phalcon\Mvc\Model\Metadata\Memory as MetaDataAdapter;

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

    $class = 'Phalcon\Db\Adapter\Pdo\\' . $config->database->mysql->adapter;
    $params = [
        'host'     => $config->database->mysql->host,
        'username' => $config->database->mysql->username,
        'password' => $config->database->mysql->password,
        'dbname'   => $config->database->mysql->dbname,
        'charset'  => $config->database->mysql->charset
    ];

    if ($config->database->mysql->adapter == 'Postgresql') {
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
    if (!empty($config->database->mongodb->username) && !empty($config->database->mongodb->password)) {
        $connectionString  .= $config->database->mongodb->username.":".$config->database->mongodb->password."@";
    }
    $connectionString .= $config->database->mongodb->host.":".$config->database->mongodb->port;
    $mongo = new \Phalcon\Db\Adapter\MongoDB\Client($connectionString);
    return $mongo->selectDatabase($config->database->mongodb->dbname);
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
 * Register cache service
 */
$di->setShared('category_cache', function () {
    $config = $this->getConfig()->category_cache;
    $cacheService = new Redis();
    if (!empty($config->auth)) {
        $cacheService->auth($config->auth);
    }
    $cacheService->pconnect(
        $config->host,
        $config->port
    );
    $cacheService->select($config->database);
    return $cacheService;
});

$di->setShared('attributes_cache', function () {
    $config = $this->getConfig()->attributes_cache;
    $cacheService = new Redis();
    if (!empty($config->auth)) {
        $cacheService->auth($config->auth);
    }
    $cacheService->pconnect(
        $config->host,
        $config->port
    );
    $cacheService->select($config->database);
    return $cacheService;
});

$di->setShared('logger', function() {
    return new \Shop_categories\Logger\ApplicationLogger();
});