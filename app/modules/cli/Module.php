<?php
/**
 * User: Wajdi Jurry
 * Date: 21/07/2018
 * Time: 01:27 PM
 */

namespace app\modules\cli;

use Phalcon\DiInterface;
use Phalcon\Loader;
use Phalcon\Mvc\ModuleDefinitionInterface;
use app\modules\cli\services\IndexingService;
use app\common\services\AttributesService;
use app\common\services\CategoryService;

class Module implements ModuleDefinitionInterface
{
    /**
     * Registers an autoloader related to the module
     *
     * @param DiInterface $di
     */
    public function registerAutoloaders(DiInterface $di = null)
    {
        $loader = new Loader();

        $loader->registerNamespaces([
            'app\modules\cli\tasks' => __DIR__ . '/tasks/',
        ]);

        $loader->register();
    }

    /**
     * Registers services related to the module
     *
     * @param DiInterface $di
     */
    public function registerServices(DiInterface $di)
    {
        // Register category service as a service
        $di->set('category', function (string $vendorId){
            $categoryService = new CategoryService();
            $categoryService::setVendorId($vendorId);
            return $categoryService;
        });

        // Register attributes service as a service
        $di->set('attributes', function (string $categoryId){
            $attributesService = new AttributesService();
            $attributesService::setCategoryId($categoryId);
            return $attributesService;
        });

        // Register indexing service as a service
        $di->set('indexing', function() {
            return new IndexingService();
        });
    }
}
