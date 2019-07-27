<?php
/**
 * User: Wajdi Jurry
 * Date: 22/08/18
 * Time: 12:07 ص
 */

namespace app\common\services;

class BaseService
{
    /**
     * @var string $vendorId
     */
    protected static $vendorId;

    /**
     * @param string $vendorId
     */
    public static function setVendorId(string $vendorId): void
    {
        self::$vendorId = $vendorId;
    }

    /**
     * @return string
     */
    public static function getVendorId(): string
    {
        return self::$vendorId;
    }
}