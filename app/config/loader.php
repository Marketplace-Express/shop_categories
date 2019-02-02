<?php

use Phalcon\Loader;

$loader = new Loader();

/**
 * Register Namespaces
 */
$loader->registerNamespaces([
    'Shop_categories\Models' => APP_PATH . '/common/models/',
    'Shop_categories\Collections' => APP_PATH . '/common/collections',
    'Shop_categories\Interfaces' => APP_PATH . '/common/interfaces/',
    'Shop_categories\Controllers' => APP_PATH . '/common/controllers/',
    'Shop_categories\Models\Behaviors' => APP_PATH . '/common/models/behaviors/',
    'Shop_categories\Helpers' => APP_PATH . '/common/helpers/',
    'Shop_categories\Traits' => APP_PATH . '/common/traits/',
    'Shop_categories\Events' => APP_PATH . '/common/events/',
    'Shop_categories\Events\Listeners' => APP_PATH . '/common/events/listeners/',
    'Shop_categories\Utils' => APP_PATH . '/common/utils/',
    'Shop_categories\Validators' => APP_PATH . '/common/validators/',
    'Shop_categories\Logger' => APP_PATH . '/common/logger/',
    'Shop_categories\Exceptions' => APP_PATH . '/common/exceptions/',
    'Shop_categories\Repositories' => APP_PATH . '/common/repositories/',
    'Shop_categories\RequestHandler' => APP_PATH . '/common/request-handler/',
    'Shop_categories\RequestHandler\Attribute' => APP_PATH . '/common/request-handler/attribute/',
    'Shop_categories\RequestHandler\Category' => APP_PATH . '/common/request-handler/category/',
    'Shop_categories\Services' => APP_PATH . '/common/services/',
    'Shop_categories\Services\Cache' => APP_PATH . '/common/services/cache',
    'Shop_categories\Services\Cache\Utils' => APP_PATH . '/common/services/cache/utils',
    'Shop_categories\DBTools' => APP_PATH . '/common/db-tools',
    'Shop_categories\DBTools\Enums' => APP_PATH . '/common/db-tools/enums',
    'Shop_categories\Modules\Api\Controllers' => APP_PATH . '/modules/api/' . $config->api->version . '/controllers',
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
    'Shop_categories\Modules\Api\Module' => APP_PATH . '/modules/api/Module.php',
    'Shop_categories\Modules\Cli\Module'      => APP_PATH . '/modules/cli/Module.php'
]);

$loader->register();
