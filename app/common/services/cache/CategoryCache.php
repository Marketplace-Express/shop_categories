<?php
/**
 * User: Wajdi Jurry
 * Date: 20/08/18
 * Time: 05:34 م
 */

namespace Shop_categories\Services\Cache;

use Shop_categories\Helpers\ArrayHelper;
use Shop_categories\Interfaces\CategoryDataSourceInterface;
use Shop_categories\Services\Cache\Utils\CategoryCacheUtils;
use Shop_categories\Services\CategoryService;

class CategoryCache implements CategoryDataSourceInterface
{
    private static $cacheKey = 'categories:vendor:%s';

    /** @var \Redis $categoryCacheInstance */
    private static $categoryCacheInstance;

    /** @var CategoryCacheUtils $categoryCacheUtils */
    private $categoryCacheUtils;

    /**
     * CategoryCache constructor.
     * @throws \Exception
     * @throws \RedisException
     */
    public function __construct()
    {
        self::establishCacheConnection();
        self::$cacheKey = sprintf(self::$cacheKey, CategoryService::getVendorId());

        if (!self::has(self::$cacheKey)) {
            // Get all categories from repository and set in cache
            $categories = CategoryService::getCategoryRepository()->getAll(CategoryService::getVendorId());
            $categories = (new ArrayHelper($categories, [
                'itemIdAttribute' => 'categoryId',
                'parentIdAttribute' => 'categoryParentId'
            ]))->tree();
            self::set(self::$cacheKey, $categories);
        }
        $this->getCategoryCacheUtils()->setArray(self::get(self::$cacheKey));
    }

    /**
     * Establish cache connection
     */
    public static function establishCacheConnection(): void
    {
        self::$categoryCacheInstance = \Phalcon\Di::getDefault()->getShared('category_cache');
    }

    /**
     * @return CategoryCacheUtils
     */
    public function getCategoryCacheUtils()
    {
        return $this->categoryCacheUtils ?? $this->categoryCacheUtils = new CategoryCacheUtils();
    }

    /**
     * Get value from cache
     * @param $key
     * @return mixed
     */
    public static function get($key)
    {
        return json_decode(self::$categoryCacheInstance->get($key), true);
    }

    /**
     * Set cache key/value pairs
     * @param $key
     * @param $value
     */
    public static function set($key, $value)
    {
        self::$categoryCacheInstance->set($key, json_encode($value));
    }

    /**
     * Check if key exists in cache
     * @param $key
     * @return bool
     */
    public static function has($key)
    {
        return self::$categoryCacheInstance->exists($key);
    }

    /**
     * @param string $categoryId
     * @param string $vendorId
     * @return array
     */
    public function getCategory(string $categoryId, ?string $vendorId = null): array
    {
        return $this->getCategoryCacheUtils()->getCategory($categoryId);
    }

    /**
     * @param string $vendorId
     * @return array
     */
    public function getRoots(?string $vendorId = null): array
    {
        return $this->getCategoryCacheUtils()->getRoots();
    }

    /**
     * @param string $categoryId
     * @param string $vendorId
     * @return array
     */
    public function getChildren(string $categoryId, ?string $vendorId = null): array
    {
        return $this->getCategoryCacheUtils()->getChildren($categoryId);
    }

    /**
     * @param string $categoryId
     * @param string $vendorId
     * @return array
     */
    public function getDescendants(string $categoryId, ?string $vendorId = null): array
    {
        return $this->getCategoryCacheUtils()->getDescendants($categoryId);
    }

    /**
     * @param string $categoryId
     * @param string $vendorId
     * @return array
     */
    public function getParents(string $categoryId, ?string $vendorId = null): array
    {
        return $this->getCategoryCacheUtils()->getParents($categoryId);
    }

    /**
     * @param string $categoryId
     * @param string $vendorId
     * @return array
     */
    public function getParent($categoryId, ?string $vendorId = null): array
    {
        return $this->getCategoryCacheUtils()->getParents($categoryId, true);
    }

    /**
     * @param string $vendorId
     * @return array
     */
    public function getAll(?string $vendorId = null): array
    {
        return $this->getCategoryCacheUtils()->getAll();
    }

    /**
     * Delete cache related to vendor
     * @return bool
     */
    public function invalidateCache()
    {
        if (self::has(self::$cacheKey)) {
            return self::$categoryCacheInstance->delete(self::$cacheKey);
        }
        return false;
    }
}