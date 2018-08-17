<?php

use Phalcon\Loader;

$loader = new Loader();

/**
 * Register Namespaces
 */
$loader->registerNamespaces([
    'Shop_categories\Models' => APP_PATH . '/common/models/',
    'Shop_categories\Traits' => APP_PATH . '/common/traits/',
    'Shop_categories\Repositories' => APP_PATH . '/common/repositories/',
    'Shop_categories\Validators' => APP_PATH . '/common/validators/',
    'Shop_categories\Modules\Frontend\Controllers' => APP_PATH . '/modules/frontend/controllers',
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
    'Shop_categories\Modules\Api\Module' => APP_PATH . '/modules/api/' . $config->api->version . '/Module.php',
    'Shop_categories\Modules\Frontend\Module' => APP_PATH . '/modules/frontend/Module.php',
    'Shop_categories\Modules\Cli\Module'      => APP_PATH . '/modules/cli/Module.php'
]);

$loader->register();
