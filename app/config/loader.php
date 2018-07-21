<?php

use Phalcon\Loader;

$loader = new Loader();

/**
 * Register Namespaces
 */
$loader->registerNamespaces([
    'Shop_categories\Models' => APP_PATH . '/common/models/',
    'Shop_categories'        => APP_PATH . '/common/library/',
]);

/**
 * Register Autoloader
 */
$loader->registerFiles([
    APP_PATH . '/common/Library/vendor/autoload.php'
]);

/**
 * Register module classes
 */
$loader->registerClasses([
    'Shop_categories\Modules\Frontend\Module' => APP_PATH . '/modules/frontend/Module.php',
    'Shop_categories\Modules\Cli\Module'      => APP_PATH . '/modules/cli/Module.php'
]);

$loader->register();
