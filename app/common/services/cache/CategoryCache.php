<?php
/**
 * User: Wajdi Jurry
 * Date: 20/08/18
 * Time: 05:34 م
 */

namespace Shop_categories\Services\Cache;

use Ehann\RediSearch\RediSearchRedisClient;
use Shop_categories\Enums\QueueNamesEnum;
use Shop_categories\Helpers\ArrayHelper;
use Shop_categories\Interfaces\CategoryDataSourceInterface;
use Shop_categories\RequestHandler\Queue\QueueRequestHandler;
use Shop_categories\Services\Cache\Utils\CategoryCacheUtils;
use Shop_categories\Services\CategoryService;

class CategoryCache implements CategoryDataSourceInterface
{

    const INDEX_NAME = 'category';

    private static $cacheKey = 'categories:vendor:%s';

    /** @var \Redis $categoryCacheInstance */
    private static $categoryCacheInstance;

    /** @var RediSearchRedisClient */
    private static $cacheIndexInstance;

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
    }

    /**
     * Establish cache connection
     */
    public static function establishCacheConnection(): void
    {
        self::$categoryCacheInstance = \Phalcon\Di::getDefault()->getShared('categoryCache');
        self::$cacheIndexInstance = \Phalcon\Di::getDefault()->getShared('categoryCacheIndex');
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
     * Prepare items in cache
     *
     * @return $this
     *
     * @throws \Exception
     */
    private function prepareCache()
    {
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
        return $this;
    }

    /**
     * @param string $categoryId
     * @param string $vendorId
     * @return array
     *
     * @throws \Exception
     */
    public function getCategory(string $categoryId, ?string $vendorId = null): array
    {
        return $this->prepareCache()->getCategoryCacheUtils()->getCategory($categoryId);
    }

    /**
     * @param string $vendorId
     * @return array
     *
     * @throws \Exception
     */
    public function getRoots(?string $vendorId = null): array
    {
        return $this->prepareCache()->getCategoryCacheUtils()->getRoots();
    }

    /**
     * @param string $categoryId
     * @param string $vendorId
     * @return array
     *
     * @throws \Exception
     */
    public function getChildren(string $categoryId, ?string $vendorId = null): array
    {
        return $this->prepareCache()->getCategoryCacheUtils()->getChildren($categoryId);
    }

    /**
     * @param string $categoryId
     * @param string $vendorId
     * @return array
     *
     * @throws \Exception
     */
    public function getDescendants(string $categoryId, ?string $vendorId = null): array
    {
        return $this->prepareCache()->getCategoryCacheUtils()->getDescendants($categoryId);
    }

    /**
     * @param string $categoryId
     * @param string $vendorId
     * @return array
     *
     * @throws \Exception
     */
    public function getParents(string $categoryId, ?string $vendorId = null): array
    {
        return $this->prepareCache()->getCategoryCacheUtils()->getParents($categoryId);
    }

    /**
     * @param string $categoryId
     * @param string $vendorId
     * @return array
     *
     * @throws \Exception
     */
    public function getParent($categoryId, ?string $vendorId = null): array
    {
        return $this->prepareCache()->getCategoryCacheUtils()->getParents($categoryId, true);
    }

    /**
     * @param string $vendorId
     * @return array
     *
     * @throws \Exception
     */
    public function getAll(?string $vendorId = null): array
    {
        return $this->prepareCache()->getCategoryCacheUtils()->getAll();
    }

    /**
     * Flush cache related to vendor
     * @return bool
     */
    public static function invalidateCache()
    {
        if (self::has(self::$cacheKey)) {
            return self::$categoryCacheInstance->delete(self::$cacheKey);
        }
        return false;
    }

    /**
     * @param array $category
     * @throws \Exception
     */
    public function indexCategory(array $category)
    {
        if (empty($category)) {
            return;
        }
        (new QueueRequestHandler())
            ->setQueueName(QueueNamesEnum::CATEGORY_ASYNC_QUEUE)
            ->setService('indexing')
            ->setMethod('add')
            ->setData([
                'id' => $category['categoryId'],
                'vendorId' => $category['categoryVendorId'],
                'name' => $category['categoryName'],
                'url' => $category['categoryUrl']
            ])
            ->sendAsync();
    }
}