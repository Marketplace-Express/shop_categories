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

define("ROOT_PATH", dirname(__DIR__));
define("TEST_PATH", ROOT_PATH . '/tests');
define("APP_PATH", ROOT_PATH . '/app');

set_include_path(
    TEST_PATH . PATH_SEPARATOR . get_include_path()
);

// Required for phalcon/incubator
include APP_PATH . "/common/library/vendor/autoload.php";

// Use the application autoloader to autoload the classes
// Autoload the dependencies found in composer
$loader = new Loader();
$loader->registerDirs(
    [
        ROOT_PATH
    ]
);

$loader->registerNamespaces([
    'app\common\models' => APP_PATH . '/common/models',
    'app\common\helpers' => APP_PATH . '/common/helpers',
    'app\common\repositories' => APP_PATH . '/common/repositories',
    'app\common\models\behaviors' => APP_PATH . '/common/models/behaviors',
    'app\common\interfaces' => APP_PATH . '/common/interfaces/',
    'app\common\dbTools' => APP_PATH . '/common/dbTools',
    'app\common\dbTools\enums' => APP_PATH . '/common/dbTools/enums',
    'app\common\traits' => APP_PATH . '/common/traits',
    'app\common\services' => APP_PATH . '/common/services',
    'app\common\services\cache' => APP_PATH . '/common/services/cache',
    'app\common\services\cache\utils' => APP_PATH . '/common/services/cache/utils',
    'app\common\requestHandler' => APP_PATH . '/common/requestHandler',
    'app\common\requestHandler\category' => APP_PATH . '/common/requestHandler/category',
    'app\modules\api\controllers' => APP_PATH . '/modules/api/controllers',
    'app\common\utils' => APP_PATH . '/common/utils',
    'app\common\logger' => APP_PATH . '/common/logger/',
    'tests\mocks' => TEST_PATH . '/mocks/'
]);

$loader->registerClasses([
    'app\common\exceptions\ArrayOfStringsException' => APP_PATH . '/common/exceptions/ArrayOfStringsException.php'
]);

$loader->register();

$di = new FactoryDefault();

Di::reset();

// Add any needed services to the DI here

Di::setDefault($di);
