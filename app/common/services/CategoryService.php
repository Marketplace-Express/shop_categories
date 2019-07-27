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
use app\common\models\Category;
use app\common\repositories\CategoryRepository;
use app\common\services\cache\CategoryCache;

class CategoryService extends AbstractService
{
    /**
     * Get stop words
     * @return array
     */
    public static function getStopWords(): array
    {
        if (file_exists($stopWords = \Phalcon\Di::getDefault()->getConfig()->application->stopWords)) {
            return json_decode(file_get_contents($stopWords), true);
        }
        return [];
    }

    /**
     * @return Category[]
     * @throws \Exception
     */
    public function getRoots()
    {
        $roots = CategoryCache::getInstance()->getRoots(self::getVendorId());
        if (!$roots) {
            $roots = CategoryRepository::getInstance()->getRoots(self::getVendorId());
        }
        return $roots;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getAll(): array
    {
        $allCategories = CategoryCache::getInstance()->getAll(self::getVendorId());
        if (!$allCategories) {
            $allCategories = CategoryRepository::getInstance()->getAll(self::getVendorId());
        }
        return $allCategories;
    }


    /**
     * @param $categoryId
     * @return array
     * @throws \Exception
     * @throws NotFoundException
     */
    public function getCategory($categoryId): array
    {
        $category = CategoryCache::getInstance()->getCategory($categoryId);
        if (!$category) {
            $category = CategoryRepository::getInstance()->getCategory($categoryId, self::getVendorId())->toApiArray();
        }
        return $category;
    }

    /**
     * @param $categoryId
     * @return array
     * @throws \Exception
     */
    public function getDescendants($categoryId): array
    {
        $descendants = CategoryCache::getInstance()->getDescendants($categoryId, self::getVendorId());
        if (!$descendants) {
            $descendants = CategoryRepository::getInstance()->getDescendants($categoryId, self::getVendorId());
        }
        return $descendants;
    }

    /**
     * @param $categoryId
     * @return array
     * @throws \Exception
     */
    public function getChildren($categoryId): array
    {
        $children = CategoryCache::getInstance()->getChildren($categoryId, self::getVendorId());
        if (!$children) {
            $children = CategoryRepository::getInstance()->getChildren($categoryId, self::getVendorId());
        }
        return $children;
    }

    /**
     * @param $categoryId
     * @return array
     * @throws \Exception
     */
    public function getParent($categoryId): array
    {
        $parent = CategoryCache::getInstance()->getParent($categoryId, self::getVendorId());
        if (1 || !$parent) {
            $parent = CategoryRepository::getInstance()->getParent($categoryId, self::getVendorId())->toApiArray();
        }
        return $parent;
    }

    /**
     * @param $categoryId
     * @return array
     * @throws \Exception
     */
    public function getParents($categoryId): array
    {
        $parents = CategoryCache::getInstance()->getParents($categoryId, self::getVendorId());
        if (!$parents) {
            $parents = CategoryRepository::getInstance()->getParents($categoryId, self::getVendorId());
        }
        return $parents;
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
     * @param string $categoryId
     * @param array $data
     * @return array
     * @throws \Exception
     */
    public function update(string $categoryId, array $data): array
    {
        $category = CategoryRepository::getInstance()->update($categoryId, self::getVendorId(), $data)->toApiArray();
        try {
            CategoryCache::getInstance()->invalidateCache();
            CategoryCache::getInstance()->updateCategoryIndex($category);
        } catch (\RedisException $exception) {
            // do nothing
        }
        return $category;
    }

    /**
     * @param $categoryId
     * @throws OperationFailedException
     * @throws NotFoundException
     * @throws ArrayOfStringsException
     */
    public function delete($categoryId): void
    {
        CategoryRepository::getInstance()->delete($categoryId, self::getVendorId());
        try {
            CategoryCache::getInstance()->invalidateCache();
            CategoryCache::getInstance()->deleteIndex($categoryId);
        } catch (\RedisException $exception) {
            // do nothing
        }
    }
}
