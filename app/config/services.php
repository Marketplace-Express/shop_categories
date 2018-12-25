<?php

use Phalcon\Mvc\Model\Metadata\Memory as MetaDataAdapter;
use Phalcon\Mvc\View\Engine\Volt as VoltEngine;


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
 * Database connection is created based in the parameters defined in the configuration file
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
 * If the configuration specify the use of metadata adapter use it or use memory otherwise
 */
$di->setShared('modelsMetadata', function () {
    return new MetaDataAdapter();
});

/**
 * Register cache service
 */
$di->setShared('cache', function () {
    $config = $this->getConfig();
    return new \Phalcon\Cache\Backend\Redis(
        new \Phalcon\Cache\Frontend\Data(['lifetime' => $config->application->categoryCacheTTL]), [
        'host' => $config->cache->host,
        'port' => $config->cache->port,
        'auth' => $config->cache->auth,
        'persistent' => $config->cache->persistent,
        'index' => $config->cache->database
    ]);
});

/**
 * Configure the Volt service for rendering .volt templates
 */
$di->setShared('voltShared', function ($view) {
    $config = $this->getConfig();

    $volt = new VoltEngine($view, $this);
    $volt->setOptions([
        'compiledPath' => function($templatePath) use ($config) {
            $basePath = $config->application->appDir;
            if ($basePath && substr($basePath, 0, 2) == '..') {
                $basePath = dirname(__DIR__);
            }

            $basePath = realpath($basePath);
            $templatePath = trim(substr($templatePath, strlen($basePath)), '\\/');

            $filename = basename(str_replace(['\\', '/'], '_', $templatePath), '.volt') . '.php';

            $cacheDir = $config->application->cacheDir;
            if ($cacheDir && substr($cacheDir, 0, 2) == '..') {
                $cacheDir = __DIR__ . DIRECTORY_SEPARATOR . $cacheDir;
            }

            $cacheDir = realpath($cacheDir);

            if (!$cacheDir) {
                $cacheDir = sys_get_temp_dir();
            }

            if (!is_dir($cacheDir . DIRECTORY_SEPARATOR . 'volt' )) {
                @mkdir($cacheDir . DIRECTORY_SEPARATOR . 'volt' , 0755, true);
            }

            return $cacheDir . DIRECTORY_SEPARATOR . 'volt' . DIRECTORY_SEPARATOR . $filename;
        }
    ]);

    return $volt;
});
