<?php
/**
 * User: Wajdi Jurry
 * Date: 28/12/18
 * Time: 12:09 م
 */

namespace Shop_categories\Services;


abstract class AbstractService extends BaseService
{
    /** @var string $categoryId */
    protected static $categoryId;

    abstract static function getDataSource();

    /**
     * @return string
     */
    public static function getCategoryId(): string
    {
        return self::$categoryId;
    }

    /**
     * @param string $categoryId
     */
    public static function setCategoryId(string $categoryId): void
    {
        self::$categoryId = $categoryId;
    }
}