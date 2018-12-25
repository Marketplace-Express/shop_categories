<?php
/**
 * User: Wajdi Jurry
 * Date: 20/08/18
 * Time: 05:34 م
 */

namespace Shop_categories\Services\Cache;

use Phalcon\Config;
use Shop_categories\Helpers\ArrayHelper;
use Shop_categories\Services\BaseService;
use Shop_categories\Services\Cache\Utils\CategoryCacheUtils;

/**
 * @method array getRoots()
 * @method array getCategory()
 * @method array getDescendants()
 * @method array getChildren()
 * @method array getParents()
 * @method array getParent()
 * @method array getAll()
 */
class CategoryCache extends BaseService
{
    private static $cacheKey = 'categories:vendor:%s';

    /**
     * CategoryCache constructor.
     */
    public function __construct()
    {
        self::$cacheInstance = self::getCacheInstance();
    }

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
            $categories = self::getRepository()->getAll(self::getVendorId());
            $categories = (new ArrayHelper($categories, [
                'itemIdAttribute' => 'categoryId',
                'parentIdAttribute' => 'categoryParentId'
            ]))->tree();
            self::set($cacheKey, $categories);
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
        return self::$cacheInstance->get($key);
    }

    /**
     * Set cache key/value pairs
     * @param $key
     * @param $value
     */
    public static function set($key, $value)
    {
        self::$cacheInstance->save($key, $value);
    }

    public static function has($key)
    {
        return self::$cacheInstance->exists($key);
    }

    /**
     * @param $operation
     * @param $value
     * @return array|bool
     * @throws \Exception
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
                throw new \Exception('Method is not callable');
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
            return self::$cacheInstance->delete($cacheKey);
        }
        return false;
    }
}