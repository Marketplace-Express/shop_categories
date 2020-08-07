<?php

use Phalcon\Events\Event;
use Phalcon\Mvc\Router;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\Session\Adapter\Files as SessionAdapter;

/**
 * Registering a router
 */
$di->setShared('router', function () {
    $router = new Router\Annotations(false);
    $router->addModuleResource('api', 'app\modules\api\controllers\Search', '/api/search');
    $router->addModuleResource('api', 'app\modules\api\controllers\Category', '/api/categories');
    $router->addModuleResource('api', 'app\modules\api\controllers\HealthCheck', '/api/health');
    return $router;
});

/**
 * The URL component is used to generate all kinds of URLs in the application
 */
$di->setShared('url', function () {
    $config = $this->getConfig();

    $url = new UrlResolver();
    $url->setBaseUri($config->application->baseUri);

    return $url;
});

/**
 * Starts the session the first time some component requests the session service
 */
$di->setShared('session', function () {
    $session = new SessionAdapter();
    $session->start();

    return $session;
});

/**
* Set the default namespace for dispatcher
*/
$di->setShared('dispatcher', function() {
    /**
     * @var \Phalcon\Events\Manager $evManager
     */
    $evManager = $this->getEventsManager();
    $evManager->attach("dispatch:beforeDispatch", function (Event $event, Dispatcher $dispatcher) {
        try {
            $methodReflection = new ReflectionMethod(
                $dispatcher->getControllerClass(),
                $dispatcher->getActiveMethod()
            );
            foreach ($methodReflection->getParameters() as $parameter) {
                $parameterClass = $parameter->getClass();
                if ($parameterClass instanceof ReflectionClass) {
                    $dispatcher->setParam($parameter->name, new $parameterClass->name);
                }
            }
        } catch (Exception $exception) {
            throw new \Exception('', Dispatcher::EXCEPTION_HANDLER_NOT_FOUND);
        }
    });
    $evManager->attach(
        "dispatch:beforeException",
        function ($event, $dispatcher, $exception) {
            /**
             * @var Exception $exception
             * @var Dispatcher $dispatcher
             */
            switch ($exception->getCode()) {
                case Dispatcher::EXCEPTION_HANDLER_NOT_FOUND:
                case Dispatcher::EXCEPTION_ACTION_NOT_FOUND:
                    $dispatcher->forward([
                        'namespace' => 'app\modules\api\controllers',
                        'controller' => 'Notfound'
                    ]);
                    return false;
            }

            switch (true) {
                case $exception instanceof \Phalcon\Mvc\Model\Exception:
                case $exception instanceof PDOException:
                    $dispatcher->forward([
                        'namespace' => 'app\modules\api\controllers',
                        'controller' => 'exceptionHandler',
                        'action' => 'raiseError',
                        'params' => [$exception->getMessage()]
                    ]);
                    return false;
            }
        }
    );
    $dispatcher = new Dispatcher();
    $dispatcher->setEventsManager($evManager);
    return $dispatcher;
});

/**
 * Json Mapper Service
 */
$di->setShared('jsonMapper', function () {
    $jsonMapper = new JsonMapper();
    $jsonMapper->bExceptionOnUndefinedProperty = false;
    $jsonMapper->bEnforceMapType = false;
    return $jsonMapper;
});