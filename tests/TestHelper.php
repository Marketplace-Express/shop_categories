<?php
/**
 * User: Wajdi Jurry
 * Date: 18/08/18
 * Time: 05:27 م
 */

use Phalcon\Di;
use Phalcon\Di\FactoryDefault;
use Phalcon\Loader;

ini_set("display_errors", 1);
error_reporting(E_ALL);

define("ROOT_PATH", __DIR__);

set_include_path(
    ROOT_PATH . PATH_SEPARATOR . get_include_path()
);

// Required for phalcon/incubator
include __DIR__ . "/../app/common/library/vendor/autoload.php";

// Use the application autoloader to autoload the classes
// Autoload the dependencies found in composer
$loader = new Loader();
$loader->registerDirs(
    [
        ROOT_PATH
    ]
);

$loader->registerNamespaces([
    'Shop_categories\Models' => ROOT_PATH . '/../app/common/models',
    'Shop_categories\Controllers' => ROOT_PATH . '/../app/common/controllers',
    'Shop_categories\Helpers' => ROOT_PATH . '/../app/common/helpers',
    'Shop_categories\Repositories' => ROOT_PATH . '/../app/common/repositories',
    'Shop_categories\Models\Behaviors' => ROOT_PATH . '/../app/common/models/behaviors',
    'Shop_categories\Interfaces' => ROOT_PATH . '/../app/common/interfaces/',
    'Shop_categories\DBTools' => ROOT_PATH . '/../app/common/db-tools',
    'Shop_categories\DBTools\Enums' => ROOT_PATH . '/../app/common/db-tools/enums',
    'Shop_categories\Traits' => ROOT_PATH . '/../app/common/traits',
    'Shop_categories\Services' => ROOT_PATH . '/../app/common/services',
    'Shop_categories\Services\Cache' => ROOT_PATH . '/../app/common/services/cache',
    'Shop_categories\Services\Cache\Utils' => ROOT_PATH . '/../app/common/services/cache/utils',
    'Shop_categories\RequestHandler' => ROOT_PATH . '/../app/common/request-handler',
    'Shop_categories\RequestHandler\Categories' => ROOT_PATH . '/../app/common/request-handler/categories',
    'Shop_categories\Modules\Api\Controllers' => ROOT_PATH . '/../app/modules/api/1.0/controllers',
    'Shop_categories\Utils' => ROOT_PATH . '/../app/common/utils',
    'Shop_categories\Logger' => ROOT_PATH . '/common/logger/'
]);

$loader->registerClasses([
    'Shop_categories\Tests\Mocks\RequestMock' => ROOT_PATH . '/mocks/RequestMock.php',
    'Shop_categories\Tests\Mocks\ResponseMock' => ROOT_PATH . '/mocks/ResponseMock.php',
    'Shop_categories\Exceptions\ArrayOfStringsException' => ROOT_PATH . '/../app/common/exceptions/ArrayOfStringsException.php'
]);

$loader->register();

$di = new FactoryDefault();

Di::reset();

// Add any needed services to the DI here

Di::setDefault($di);
