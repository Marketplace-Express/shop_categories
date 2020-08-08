<?php
/**
 * User: Wajdi Jurry
 * Date: 20/08/18
 * Time: 05:29 م
 */

namespace app\common\services;

use app\common\exceptions\ArrayOfStringsException;
use app\common\exceptions\NotFoundException;
use app\common\exceptions\OperationFailedException;
use app\common\helpers\ArrayHelper;
use app\common\repositories\CategoryRepository;
use app\common\services\cache\CategoryCache;
use app\common\utils\UuidUtil;

class CategoryService extends AbstractService
{
    /** @var AttributesService */
    private $attributesService;

    /**
     * @return AttributesService
     */
    protected function getAttributesService(): AttributesService
    {
        return $this->attributesService ?? $this->attributesService = new AttributesService();
    }

    /**
     * Get stop words
     * @return array
     */
    public static function getStopWords(): array
    {
        /** @noinspection PhpUndefinedMethodInspection */
        /** @noinspection PhpFullyQualifiedNameUsageInspection */
        if (file_exists($stopWords = \Phalcon\Di::getDefault()->getConfig()->application->stopWords)) {
            return json_decode(file_get_contents($stopWords), true);
        }
        return [];
    }

    /**
     * @param array $ids
     * @return array
     * @throws \RedisException
     * @throws \Exception
     */
    public function getCategories(array $ids = [])
    {
        if (!empty($ids)) {
            $categories = CategoryCache::getInstance()->getByIds($ids);
        } else {
            $categories = CategoryCache::getInstance()->getAll(self::getStoreId());
        }
        return $categories;
    }

    /**
     * @param string $storeId
     * @return array
     * @throws \RedisException
     * @throws \Exception
     */
    public function getByStoreId(string $storeId): array
    {
        self::setStoreId($storeId);
        return CategoryCache::getInstance()->getAll($storeId);
    }

    /**
     * @param $categoryId
     * @return array
     * @throws \Exception
     */
    public function getChildren($categoryId): array
    {
        return CategoryCache::getInstance()->getChildren($categoryId, self::getStoreId());
    }

    /**
     * @param $categoryId
     * @return array
     * @throws \Exception
     */
    public function getParent($categoryId): array
    {
        return CategoryCache::getInstance()->getParent($categoryId, self::getStoreId());
    }

    /**
     * Create category
     * @param array $data
     * @return array
     * @throws \Exception
     */
    public function create(array $data): array
    {
        $category = CategoryRepository::getInstance()->create($data)->toApiArray();
        if (!empty($data['attributes'])) {
            $this->getAttributesService()->create($data['attributes'], $category['id']);
        }
        try {
            CategoryCache::getInstance()->invalidateCache();
            CategoryCache::getInstance()->indexCategory($category);
        } catch (\RedisException $exception) {
            // do nothing
        }
        return $category;
    }

    /**
     * Update category
     * @param array $data
     * @return array
     * @throws \Exception
     */
    public function update(array $data): array
    {
        $category = CategoryRepository::getInstance()->update($data['id'], self::getStoreId(), $data)->toApiArray();
        if (!empty($data['attributes'])) {
            foreach ($data['attributes'] as $attribute) {
                if (!empty($attribute['attribute_id'])) {
                    $this->getAttributesService()->update([$attribute], $category['id']);
                } else {
                    $this->getAttributesService()->create([$attribute], $data['id']);
                }
            }
        }
        try {
            CategoryCache::getInstance()->invalidateCache();
            CategoryCache::getInstance()->updateCategoryIndex($category);
        } catch (\RedisException $exception) {
            // do nothing
        }
        return $category;
    }

    /**
     * @param array $data
     * @return bool
     *
     * @throws ArrayOfStringsException
     * @throws NotFoundException
     * @throws OperationFailedException
     */
    public function delete(array $data): bool
    {
        $isDeleted = CategoryRepository::getInstance()->delete($data['id'], self::getStoreId());
        try {
            CategoryCache::getInstance()->invalidateCache();
            CategoryCache::getInstance()->deleteIndex($data['id']);
        } catch (\RedisException $exception) {
            // do nothing
        }
        return $isDeleted;
    }
}
