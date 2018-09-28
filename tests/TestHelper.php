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
    'Shop_categories\Modules\Api\Controllers' => ROOT_PATH . '/../app/modules/api/1.0/controllers',
    'Shop_categories\Services' => ROOT_PATH . '/../app/common/services',
    'Shop_categories\RequestHandler' => ROOT_PATH . '/../app/common/request-handler',
]);

$loader->register();

$di = new FactoryDefault();

Di::reset();

// Add any needed services to the DI here

Di::setDefault($di);
