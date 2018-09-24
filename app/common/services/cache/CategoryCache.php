<?php
/**
 * User: Wajdi Jurry
 * Date: 20/08/18
 * Time: 05:34 م
 */

namespace Shop_categories\Services\Cache;

use Prophecy\Exception\Doubler\MethodNotFoundException;
use Shop_categories\Services\BaseService;
use Shop_categories\Services\Cache\Utils\CategoryCacheUtils;

/**
 * @method static array getRoots()
 * @method static array getCategory()
 * @method static array getDescendants()
 * @method static array getChildren()
 * @method static array getParents()
 * @method static array getParent()
 * @method static array getAll()
 */
class CategoryCache extends BaseService
{
    private static $cacheKey = 'categories:vendor:%s';

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     * @throws \Exception
     */
    public function __call($name, $arguments)
    {
        // Get cache key for requested action
        $cacheKey = sprintf(self::$cacheKey, self::getVendorId());

        if (!self::has($cacheKey)) {
            // Get all categories from repository and set in cache
            self::set($cacheKey, self::getRepository()->getAll(self::getVendorId()));
        }

        // Process data from cache
        return self::processData($name, self::get($cacheKey));
    }

    /**
     * Get value from cache
     * @param $key
     * @return mixed
     */
    public static function get($key)
    {
        return self::getCacheInstance()->get($key);
    }

    /**
     * Set cache key/value pairs
     * @param $key
     * @param $value
     */
    public static function set($key, $value)
    {
        self::getCacheInstance()->save($key, $value, -1);
    }

    public static function has($key)
    {
        return self::getCacheInstance()->exists($key);
    }

    /**
     * @param $operation
     * @param $value
     * @return array|bool
     * @throws MethodNotFoundException
     */
    public static function processData($operation, $value)
    {
        $cacheUtils = (new CategoryCacheUtils())->setArray($value);
        switch ($operation) {
            case 'getAll':
                return $cacheUtils->getAll();
                break;
            case 'getRoots':
                return $cacheUtils->getRoots();
                break;
            case 'getDescendants':
            case 'getChildren':
            case 'getParents':
            case 'getParent':
            case 'getCategory':
                return $cacheUtils->getCategory(self::getCategoryId(), $operation);
                break;
            default:
                throw new MethodNotFoundException('Method is not callable', self::class, $operation);
        }
    }

    /**
     * Delete cache related to vendor
     * @return bool
     */
    public function invalidateCache()
    {
        $cacheKey = sprintf(self::$cacheKey, self::getVendorId());
        if (self::has($cacheKey)) {
            return self::getCacheInstance()->delete($cacheKey);
        }
        return false;
    }
}