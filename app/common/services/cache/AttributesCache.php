<?php
/**
 * User: Wajdi Jurry
 * Date: 25/12/18
 * Time: 11:53 م
 */

namespace app\common\services\cache;

use app\common\interfaces\AttributeDataSourceInterface;

class AttributesCache implements AttributeDataSourceInterface
{
    const ATTRIBUTES_CACHE_CONFIG_KEY = 'attributesCache';

    /** @var \Redis $attributesCacheInstance */
    protected static $attributesCacheInstance;

    private static $attributeCacheKey = 'attribute:%s';
    private static $categoryCacheKey = 'category:%s';

    /**
     * @return AttributesCache
     * @throws \RedisException
     */
    static public function getInstance()
    {
        self::establishCacheConnection();
        return new self;
    }

    public function getAttributeCacheKey(string $attributeId)
    {
        return sprintf(self::$attributeCacheKey, $attributeId);
    }

    public function getCategoryCacheKey(string $categoryId)
    {
        return sprintf(self::$categoryCacheKey, $categoryId);
    }

    /**
     * AttributesCache constructor.
     * @throws \RedisException
     */
    public function __construct()
    {
        self::establishCacheConnection();
    }

    /**
     * Establish cache connection
     */
    public static function establishCacheConnection(): void
    {
        self::$attributesCacheInstance = \Phalcon\Di::getDefault()->getShared(self::ATTRIBUTES_CACHE_CONFIG_KEY);
    }

    /**
     * Get value key
     * @param $key
     * @return mixed|null
     */
    public static function get($key)
    {
        return json_decode(self::$attributesCacheInstance->get($key), true);
    }

    /**
     * Get hashKey
     * @param $key
     * @param $hashKey
     * @return array
     */
    public static function hGet($key, $hashKey)
    {
        return json_decode(self::$attributesCacheInstance->hGet($key, $hashKey), true);
    }

    public static function hGetAll($key)
    {
        $result = self::$attributesCacheInstance->hGetAll($key);
        foreach ($result as &$item) {
            $item = json_decode($item, true);
        }
        return array_values($result);
    }

    public static function hDelete($key, ...$hashKeys)
    {
        return call_user_func_array([self::$attributesCacheInstance, 'hDel'], array_merge([$key], $hashKeys));
    }

    /**
     * Set cache key
     * @param $key
     * @param $value
     */
    public static function set($key, $value)
    {
        self::$attributesCacheInstance->set($key, json_encode($value));
    }

    /**
     * Set hashKey
     * @param $key
     * @param $hashKey
     * @param $value
     */
    public static function hSet(string $key, string $hashKey, $value)
    {
        self::$attributesCacheInstance->hSet($key, $hashKey, json_encode($value));
    }

    /**
     * Check if key exists in cache
     * @param $key
     * @return bool
     */
    public static function exists(string $key)
    {
        return self::$attributesCacheInstance->exists($key);
    }

    /**
     * Check if hashKey exists
     * @param string $key
     * @param string $hashKey
     * @return bool
     */
    public static function hExists(string $key, string $hashKey)
    {
        return self::$attributesCacheInstance->hExists($key, $hashKey);
    }

    /**
     * Delete keys
     * @param string ...$cacheKeys
     * @return int
     */
    public function delete(string ...$cacheKeys)
    {
        return self::$attributesCacheInstance->delete($cacheKeys);
    }

    /**
     * @param string $attributeId
     * @return array|null
     * @throws \Exception
     */
    public function getAttribute(string $attributeId)
    {
        $cacheKey = $this->getAttributeCacheKey($attributeId);
        return self::get($cacheKey);
    }

    /**
     * Get all attributes related to category
     * @param string $categoryId
     * @return array
     * @throws \Exception
     */
    public function getAll(string $categoryId): array
    {
        return self::hGetAll($this->getCategoryCacheKey($categoryId));
    }

    /**
     * Get attribute values
     *
     * @param string $attributeId
     * @return array
     *
     * @throws \Exception
     */
    public function getValues(string $attributeId): array
    {
        $attribute = $this->getAttribute($attributeId);
        if ($attribute) {
            return $attribute['attributeValues'];
        }
        return null;
    }

    /**
     * @param string $attributeId
     * @param string $categoryId
     * @param array $data
     * @return bool
     */
    public function updateCache(string $attributeId, string $categoryId, array $data)
    {
        $updateAttributeCache = self::set($this->getAttributeCacheKey($attributeId), $data);
        $updateCategoryCache = self::hSet($this->getCategoryCacheKey($categoryId), $attributeId, $data);
        if ($updateAttributeCache && $updateCategoryCache) {
            return true;
        }
        \Phalcon\Di::getDefault()->get('logger')->logError('Attribute ' . $attributeId . ' not added/updated in cache.');
        return false;
    }

    /**
     * @param string $attributeId
     * @return bool
     */
    public function invalidateCache(string $attributeId)
    {
        $attributeCacheKey = $this->getAttributeCacheKey($attributeId);
        if (self::exists($attributeCacheKey)) {
            $attribute = self::get($attributeCacheKey);
            $deleteAttribute =  self::$attributesCacheInstance->delete($attributeCacheKey);
            $deleteFromCategory = self::hDelete($this->getCategoryCacheKey($attribute['attributeCategoryId']), $attributeId);
            if ($deleteAttribute && $deleteFromCategory) {
                return true;
            }
        }
        \Phalcon\Di::getDefault()->get('logger')->logError('Attribute ' . $attributeId . ' not deleted from cache or already deleted.');
        return false;
    }
}