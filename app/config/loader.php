<?php

use Phalcon\Loader;

$loader = new Loader();

/**
 * Register Namespaces
 */
$loader->registerNamespaces([
    'app\common\models' => APP_PATH . '/common/models/',
    'app\common\collections' => APP_PATH . '/common/collections/',
    'app\common\interfaces' => APP_PATH . '/common/interfaces/',
    'app\common\controllers' => APP_PATH . '/common/controllers/',
    'app\common\models\behaviors' => APP_PATH . '/common/models/behaviors/',
    'app\common\helpers' => APP_PATH . '/common/helpers/',
    'app\common\traits' => APP_PATH . '/common/traits/',
    'app\common\events' => APP_PATH . '/common/events/',
    'app\common\events\listeners' => APP_PATH . '/common/events/listeners/',
    'app\common\events\middleware' => APP_PATH . '/common/events/middleware/',
    'app\common\utils' => APP_PATH . '/common/utils/',
    'app\common\validators' => APP_PATH . '/common/validators/',
    'app\common\logger' => APP_PATH . '/common/logger/',
    'app\common\exceptions' => APP_PATH . '/common/exceptions/',
    'app\common\repositories' => APP_PATH . '/common/repositories/',
    'app\common\requestHandler' => APP_PATH . '/common/requestHandler/',
    'app\common\requestHandler\attribute' => APP_PATH . '/common/requestHandler/attribute/',
    'app\common\requestHandler\category' => APP_PATH . '/common/requestHandler/category/',
    'app\common\requestHandler\queue' => APP_PATH . '/common/requestHandler/queue/',
    'app\common\services' => APP_PATH . '/common/services/',
    'app\common\services\cache' => APP_PATH . '/common/services/cache/',
    'app\common\services\cache\utils' => APP_PATH . '/common/services/cache/utils/',
    'app\common\services\user' => APP_PATH . '/common/services/user/',
    'app\common\dbTools' => APP_PATH . '/common/dbTools/',
    'app\common\dbTools\enums' => APP_PATH . '/common/dbTools/enums/',
    'app\common\enums' => APP_PATH . '/common/enums/',
    'app\modules\api\controllers' => APP_PATH . '/modules/api/' . $config->api->version . '/controllers/',
    'app\modules\cli\request' => APP_PATH . '/modules/cli/request/',
    'app\modules\cli\services' => APP_PATH . '/modules/cli/services/',
    'app\common\redis' => APP_PATH . '/common/redis/',
    'app\common\graphqlTypes' => APP_PATH . '/common/graphqlTypes'
]);

/**
 * Register Vendors
 */
$loader->registerFiles([
    APP_PATH . '/common/library/vendor/autoload.php'
]);

/**
 * Register module classes
 */
$loader->registerClasses([
    'app\modules\api\Module' => APP_PATH . '/modules/api/Module.php',
    'app\modules\cli\Module'      => APP_PATH . '/modules/cli/Module.php'
]);

$loader->register();
