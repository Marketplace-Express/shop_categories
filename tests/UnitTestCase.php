<?php

use Phalcon\Config\Adapter\Yaml;
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
        $di->set('request', new RequestMock());
        $di->set('response', new ResponseMock());
        $di->set('config', function () {
            $config = new Yaml(APP_PATH . '/config/config.yml', [
                '!appDir' => function ($value) {
                    return APP_PATH . $value ;
                },
                '!baseDir' => function ($value) {
                    return BASE_PATH . $value;
                }
            ]);
            return $config;
        });

        $this->setDi($di);
    }

    protected function createTables()
    {
        $this->pdo->query(sprintf("CREATE TABLE IF NOT EXISTS %s (
            %s VARCHAR(10), %s VARCHAR(10)
        )", static::TABLE_NAME, static::COLUMNS[0], static::COLUMNS[1]));
        $this->pdo->query(sprintf("CREATE TABLE IF NOT EXISTS %s (
            %s VARCHAR(10), %s VARCHAR(10)
        )", static::ANOTHER_TABLE_NAME, static::COLUMNS[0], static::COLUMNS[1]));
    }
}