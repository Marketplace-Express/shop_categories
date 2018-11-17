<?php

use Phalcon\Di\FactoryDefault;
use Phalcon\Mvc\Application;

error_reporting(E_ALL);

define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');

try {

    /**
     * The FactoryDefault Dependency Injector automatically registers the services that
     * provide a full stack framework. These default services can be overidden with custom ones.
     */
    $di = new FactoryDefault();

    /**
     * Include general services
     */
    require APP_PATH . '/config/services.php';

    /**
     * Include web environment specific services
     */
    require APP_PATH . '/config/services_web.php';

    /**
     * Get config service for use in inline setup below
     */
    $config = $di->getConfig();

    /**
     * Include Autoloader
     */
    include APP_PATH . '/config/loader.php';

    /**
     * Handle the request
     */
    $application = new Application($di);

    /**
     * Register application modules
     */
    $application->registerModules([
        'api' => ['className' => 'Shop_categories\Modules\Api\Module'],
        'frontend' => ['className' => 'Shop_categories\Modules\Frontend\Module'],
        'cli' => ['className' => 'Shop_categories\Modules\Cli\Module']
    ]);

    /**
     * Disable view service
     */
    $application->useImplicitView(false);

    /**
     * Include routes
     */
//    require APP_PATH . '/config/routes.php';

    echo ($application->handle()->getContent());

} catch (\Exception $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['status' => 500, 'message' => $e->getMessage()]);
}
