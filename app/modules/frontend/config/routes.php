<?php
/**
 * Created by PhpStorm.
 * User: wajdi
 * Date: 26/07/18
 * Time: 11:32 م
 */

$router = new \Phalcon\Mvc\Router();

$group = new \Phalcon\Mvc\Router\Group([
    'module' => 'frontend',
    'controller' => 'index'
]);