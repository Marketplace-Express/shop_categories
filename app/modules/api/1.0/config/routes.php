<?php
/**
 * User: Wajdi Jurry
 * Date: 26/07/18
 * Time: 11:32 م
 */

$config = $di->get('config');

$router = new \Phalcon\Mvc\Router();

$group = new \Phalcon\Mvc\Router\Group([
    'module' => 'api',
    'controller' => 'index'
]);

$group->setPrefix('/api/' . $config->api->version);

//$group->add('/api/'.$config->apiVersion.'/:controller/:action/:params', [
//    'namespace' => 'Shop_categories\Modules\Api\Module',
//    'module' => 'api',
//    'controller' => 1,
//    'action' => 2,
//    'params' => 3
//]);

$loader = new \Phalcon\Loader();
$loader->registerNamespaces([
    'Shop_categories\Modules\Api\Controllers' => APP_PATH . '/modules/api/' . $config->api->version . '/controllers'
]);

$loader->register();