<?php
/**
 * User: Wajdi Jurry
 * Date: 20/08/18
 * Time: 05:34 م
 */

namespace app\common\services\cache;

use Phalcon\Di;
use app\common\enums\QueueNamesEnum;
use app\common\helpers\ArrayHelper;
use app\common\interfaces\CategoryDataSourceInterface;
use app\common\repositories\CategoryRepository;
use app\common\requestHandler\queue\QueueRequestHandler;
use app\common\services\cache\utils\CategoryCacheUtils;
use app\common\services\CategoryService;

class CategoryCache implements CategoryDataSourceInterface
{

    const INDEX_NAME = 'category';

    private static $cacheKey = 'categories:store:%s';

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
        self::$cacheKey = sprintf(self::$cacheKey, CategoryService::getstoreId());
    }

    /**
     * @return CategoryCache
     * @throws \RedisException
     */
    static public function getInstance()
    {
        return new self;
    }

    /**
     * Establish cache connection
     */
    public static function establishCacheConnection(): void
    {
        self::$categoryCacheInstance = Di::getDefault()->getShared('categoryCache');
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
            $categories = CategoryRepository::getInstance()->getAll(CategoryService::getstoreId());
            $categories = (new ArrayHelper($categories, [
                'itemIdAttribute' => 'id',
                'parentIdAttribute' => 'parentId'
            ]))->tree();
            self::set(self::$cacheKey, $categories);
        }
        $this->getCategoryCacheUtils()->setArray(self::get(self::$cacheKey));
        return $this;
    }

    /**
     * @param string $categoryId
     * @param string $storeId
     * @return array
     *
     * @throws \Exception
     */
    public function getCategory(string $categoryId, ?string $storeId = null): array
    {
        return $this->prepareCache()->getCategoryCacheUtils()->getCategory($categoryId);
    }

    /**
     * @param string $storeId
     * @return array
     *
     * @throws \Exception
     */
    public function getRoots(?string $storeId = null): array
    {
        return $this->prepareCache()->getCategoryCacheUtils()->getRoots();
    }

    /**
     * @param string $categoryId
     * @param string $storeId
     * @return array
     *
     * @throws \Exception
     */
    public function getChildren(string $categoryId, ?string $storeId = null): array
    {
        return $this->prepareCache()->getCategoryCacheUtils()->getChildren($categoryId);
    }

    /**
     * @param string $categoryId
     * @param string $storeId
     * @return array
     *
     * @throws \Exception
     */
    public function getDescendants(string $categoryId, ?string $storeId = null): array
    {
        return $this->prepareCache()->getCategoryCacheUtils()->getDescendants($categoryId);
    }

    /**
     * @param string $categoryId
     * @param string $storeId
     * @return array
     *
     * @throws \Exception
     */
    public function getParents(string $categoryId, ?string $storeId = null): array
    {
        return $this->prepareCache()->getCategoryCacheUtils()->getParents($categoryId);
    }

    /**
     * @param string $categoryId
     * @param string $storeId
     * @return array
     *
     * @throws \Exception
     */
    public function getParent($categoryId, ?string $storeId = null): array
    {
        return $this->prepareCache()->getCategoryCacheUtils()->getParents($categoryId, true);
    }

    /**
     * @param string $storeId
     * @return array
     *
     * @throws \Exception
     */
    public function getAll(?string $storeId = null): array
    {
        return $this->prepareCache()->getCategoryCacheUtils()->getAll();
    }

    /**
     * @param array $ids
     * @return array
     * @throws \Exception
     */
    public function getByIds(array $ids)
    {
        return $this->prepareCache()->getCategoryCacheUtils()->getByIds($ids);
    }

    /**
     * Flush cache related to store
     * @return bool
     */
    public static function invalidateCache()
    {
        if (self::has(self::$cacheKey)) {
            return self::$categoryCacheInstance->del(self::$cacheKey);
        }
        return false;
    }

    /**
     * @param array $category
     * @throws \Exception
     */
    public function indexCategory(array $category): void
    {
        if (empty($category)) {
            return;
        }
        (new QueueRequestHandler())
            ->setQueueName(QueueNamesEnum::CATEGORY_ASYNC_QUEUE)
            ->setService('indexing')
            ->setMethod('add')
            ->setData([
                'id' => $category['id'],
                'storeId' => $category['storeId'],
                'name' => $category['name'],
                'url' => $category['url']
            ])
            ->sendAsync();
    }

    /**
     * @param array $category
     * @throws \app\common\exceptions\ArrayOfStringsException
     */
    public function updateCategoryIndex(array $category): void
    {
        if (empty($category)) {
            return;
        }

        (new QueueRequestHandler())
            ->setQueueName(QueueNamesEnum::CATEGORY_ASYNC_QUEUE)
            ->setService('indexing')
            ->setMethod('update')
            ->setData([
                'id' => $category['id'],
                'storeId' => $category['storeId'],
                'name' => $category['name'],
                'url' => $category['url']
            ])
            ->sendAsync();
    }

    /**
     * @param string $categoryId
     * @throws \app\common\exceptions\ArrayOfStringsException
     */
    public function deleteIndex(string $categoryId): void
    {
        (new QueueRequestHandler())
            ->setQueueName(QueueNamesEnum::CATEGORY_ASYNC_QUEUE)
            ->setService('indexing')
            ->setMethod('delete')
            ->setData([
                'id' => $categoryId
            ])
            ->sendAsync();
    }
}
