<?php


namespace tests\mocks\services;


class BaseService
{
    private static $vendorId;

    static public function setVendorId(string $vendorId)
    {
        self::$vendorId = $vendorId;
    }
}