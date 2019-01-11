<?php
/**
 * User: Wajdi Jurry
 * Date: 22/08/18
 * Time: 12:07 ص
 */

namespace Shop_categories\Services;

use Phalcon\Cache\Backend\Redis;
use Shop_categories\Models\Behaviors\AdjacencyListModelBehavior;
use Shop_categories\Repositories\AttributesRepository;
use Shop_categories\Repositories\CategoryRepository;
use Shop_categories\Services\Cache\AttributesCache;
use Shop_categories\Services\Cache\CategoryCache;

class BaseService
{
    const CATEGORY_CACHE_CONFIG_KEY = 'category_cache';
    const ATTRIBUTES_CACHE_CONFIG_KEY = 'attributes_cache';
    /**
     * @var Redis $categoryCacheInstance
     */
    protected static $categoryCacheInstance;

    /**
     * @var CategoryCache $categoryCacheService
     */
    public static $categoryCacheService;

    /**
     * @var AttributesCache $attributesCacheService
     */
    public static $attributesCacheService;

    /**
     * @var CategoryRepository $categoryRepository
     */
    public static $categoryRepository;

    /**
     * @var AttributesRepository $attributesRepository
     */
    public static $attributesRepository;

    /**
     * @return CategoryRepository
     */
    public static function getCategoryRepository(): CategoryRepository
    {
        return self::$categoryRepository ?? new CategoryRepository();
    }

    /**
     * @return AttributesRepository
     */
    public static function getAttributesRepository(): AttributesRepository
    {
        return self::$attributesRepository ?? new AttributesRepository();
    }

    /**
     * @return CategoryCache
     * @throws \Exception
     * @throws \RedisException
     */
    public static function getCategoryCache(): CategoryCache
    {
        if (!self::$categoryCacheService) {
            self::$categoryCacheService = new CategoryCache();
        }
        return self::$categoryCacheService;
    }

    /**
     * @return AttributesCache
     * @throws \RedisException
     */
    public static function getAttributesCacheService(): AttributesCache
    {
        if (!self::$attributesCacheService) {
            self::$attributesCacheService = new AttributesCache();
        }
        return self::$attributesCacheService;
    }
}