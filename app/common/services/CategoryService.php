<?php
/**
 * User: Wajdi Jurry
 * Date: 20/08/18
 * Time: 05:29 م
 */

namespace Shop_categories\Services;

use RedisException;
use Shop_categories\Logger\ApplicationLogger;
use Shop_categories\Exceptions\ArrayOfStringsException;
use Shop_categories\Models\Category;
use Shop_categories\Repositories\CategoryRepository;

class CategoryService extends BaseService
{
    /**
     * @return CategoryRepository|Cache\CategoryCache
     * @throws \Exception
     */
    protected function getDataSource()
    {
        try {
            return self::getCacheService();
        } catch (RedisException $exception) {
            return self::getRepository();
        } catch (\Throwable $exception) {
            throw new \Exception('No data source available');
        }
    }

    /**
     * @return Category[]
     * @throws \Exception
     */
    public function getRoots()
    {
        if ($roots = self::getDataSource()->getRoots(self::getVendorId())) {
            return $roots;
        }

        throw new \Exception('No roots found', 404);
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getAll(): array
    {
        if ($allCategories = self::getDataSource()->getAll(self::getVendorId())) {
            return $allCategories;
        }

        throw new \Exception('No categories found', 404);
    }


    /**
     * @param $categoryId
     * @return array
     * @throws \Exception
     */
    public function getCategory($categoryId): array
    {
        self::setCategoryId($categoryId);

        if ($category = self::getDataSource()->getCategory($categoryId, self::getVendorId())) {
            return $category;
        }

        throw new \Exception('Category not found', 404);
    }

    /**
     * @param $categoryId
     * @return array
     * @throws \Exception
     */
    public function getDescendants($categoryId): array
    {
        self::setCategoryId($categoryId);
        return self::getDataSource()->getDescendants($categoryId, self::getVendorId());
    }

    /**
     * @param $categoryId
     * @return array
     * @throws \Exception
     */
    public function getChildren($categoryId): array
    {
        self::setCategoryId($categoryId);
        return self::getDataSource()->getChildren($categoryId, self::getVendorId());
    }

    /**
     * @param $categoryId
     * @return array
     * @throws \Exception
     */
    public function getParent($categoryId): array
    {
        self::setCategoryId($categoryId);
        return self::getDataSource()->getParent($categoryId, self::getVendorId());
    }

    /**
     * @param $categoryId
     * @return array
     * @throws \Exception
     */
    public function getParents($categoryId): array
    {
        self::setCategoryId($categoryId);
        return self::getDataSource()->getParents($categoryId, self::getVendorId());
    }

    /**
     * Create category
     * @param array $data
     * @return array
     * @throws \Exception
     */
    public function create(array $data): array
    {
        if ($category = self::getRepository()->create($data)) {
            $this->invalidateCache();
            return $category->toApiArray();
        }

        throw new ArrayOfStringsException($category->getMessages(), 400);
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
        if ($category = self::getRepository()->update($categoryId, self::getVendorId(), $data)) {
            $this->invalidateCache();
            return $category->toApiArray();
        }

        throw new ArrayOfStringsException($category->getMessages(), 400);
    }

    /**
     * @param $categoryId
     * @throws \Exception
     */
    public function delete($categoryId): void
    {
        if (self::getRepository()->delete($categoryId, self::getVendorId())) {
            $this->invalidateCache();
        } else {
            throw new \Exception('Category not found or maybe deleted', 404);
        }
    }

    /**
     * Invalidate cache after any DB operation (insert, update or delete)
     */
    private function invalidateCache()
    {
        try {
            self::getCacheService()->invalidateCache();
        } catch (\RedisException $exception) {
            (new ApplicationLogger())->logError('Invalidate cache: ' . $exception->getMessage());
        }
    }
}
