<?php

namespace tests;

use Phalcon\Di;
use Phalcon\Test\UnitTestCase as PhalconTestCase;
use tests\mocks\RequestMock;
use tests\mocks\ResponseMock;

abstract class UnitTestCase extends PhalconTestCase
{
    public function setUp()
    {
        parent::setUp();

        // Load any additional services that might be required during testing
        $di = Di::getDefault();

        // Get any DI components here. If you have a config, be sure to pass it to the parent
        $di->setShared('request', RequestMock::class);
        $di->setShared('response', ResponseMock::class);
        $di->setShared('config', function () {
            return require APP_PATH . '/config/config.php';
        });
        $di->set('jsonMapper', \JsonMapper::class);
        $di->set('appServices', function($serviceName) {
            $services = [
                'categoryService' => 'tests\mocks\services\CategoryService',
                'attributeService' => 'tests\mocks\services\AttributesService',
                'searchService' => 'tests\mocks\services\SearchService'
            ];

            return new $services[$serviceName];
        });


        $this->setDi($di);
    }
}