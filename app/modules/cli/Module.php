<?php
namespace Shop_categories\Modules\Cli;

use Phalcon\DiInterface;
use Phalcon\Loader;
use Phalcon\Mvc\ModuleDefinitionInterface;
use Shop_categories\Modules\Cli\Services\IndexingService;
use Shop_categories\Services\AttributesService;
use Shop_categories\Services\CategoryService;

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
            'Shop_categories\Modules\Cli\Tasks' => __DIR__ . '/tasks/',
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
