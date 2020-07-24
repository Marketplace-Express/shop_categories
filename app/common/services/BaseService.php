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
     * @var string $storeId
     */
    protected static $storeId;

    /**
     * @param string $storeId
     */
    public static function setstoreId(string $storeId): void
    {
        self::$storeId = $storeId;
    }

    /**
     * @return string
     */
    public static function getstoreId(): string
    {
        return self::$storeId;
    }
}