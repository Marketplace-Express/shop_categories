<?php


namespace tests\mocks\services;


class BaseService
{
    private static $storeId;

    static public function setStoreId(string $storeId)
    {
        self::$storeId = $storeId;
    }
}