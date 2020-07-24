<?php


namespace tests\mocks\services;


class BaseService
{
    private static $storeId;

    static public function setstoreId(string $storeId)
    {
        self::$storeId = $storeId;
    }
}