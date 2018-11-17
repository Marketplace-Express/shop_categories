<?php
/**
 * User: Wajdi Jurry
 * Date: 22/08/18
 * Time: 12:07 ص
 */

namespace Shop_categories\Services;

use Phalcon\Cache\Backend\Redis;
use Phalcon\Mvc\Model\Behavior\NestedSet;
use Shop_categories\Models\Behaviors\AdjacencyListModelBehavior;
use Shop_categories\Repositories\CategoryRepository;
use Shop_categories\Services\Cache\CategoryCache;

class BaseService
{
    /**
     * @var Redis $cacheInstance
     */
    public static $cacheInstance;

    /**
     * @var CategoryCache $cacheService
     */
    public static $cacheService;

    /**
     * @var CategoryRepository|NestedSet $repository
     */
    public static $repository;

    /**
     * @var string $vendorId
     */
    public static $vendorId;

    /**
     * @var string $categoryId
     */
    public static $categoryId;

    /**
     * @return CategoryRepository|AdjacencyListModelBehavior
     */
    public static function getRepository()
    {
        if (!self::$repository) {
            self::$repository = new CategoryRepository();
        }

        return self::$repository;
    }

    /**
     * @return Redis
     */
    public static function getCacheInstance(): Redis
    {
        if (!self::$cacheInstance) {
            self::$cacheInstance = \Phalcon\Di::getDefault()->getShared('cache');
        }

        return self::$cacheInstance;
    }


    /**
     * @return CategoryCache
     */
    public static function getCacheService(): CategoryCache
    {
        if (!self::$cacheService) {
            self::$cacheService = new CategoryCache();
        }
        return self::$cacheService;
    }

    /**
     * @return string
     */
    public static function getVendorId(): string
    {
        return self::$vendorId;
    }

    /**
     * @param string $vendorId
     */
    public function setVendorId(string $vendorId): void
    {
        self::$vendorId = $vendorId;
    }

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