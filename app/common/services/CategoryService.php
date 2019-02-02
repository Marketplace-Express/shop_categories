<?php
/**
 * User: Wajdi Jurry
 * Date: 20/08/18
 * Time: 05:29 م
 */

namespace Shop_categories\Services;

use RedisException;
use Shop_categories\Exceptions\ArrayOfStringsException;
use Shop_categories\Models\Category;

class CategoryService extends AbstractService
{
    /** @var string $vendorId */
    protected static $vendorId;

    /**
     * @param string $vendorId
     */
    public static function setVendorId(string $vendorId): void
    {
        self::$vendorId = $vendorId;
    }

    /**
     * @return string
     */
    public static function getVendorId(): string
    {
        return self::$vendorId;
    }

    /**
     * @return \Shop_categories\Repositories\CategoryRepository|Cache\CategoryCache
     * @throws \Exception
     */
    private function getCategoryDataSource()
    {
        try {
            return self::getCategoryCache();
        } catch (RedisException $exception) {
            return self::getCategoryRepository();
        } catch (\Throwable $exception) {
            throw new \Exception('No data source available for categories');
        }
    }

    /**
     * @return Category[]
     * @throws \Exception
     */
    public function getRoots()
    {
        if ($roots = self::getCategoryDataSource()->getRoots(self::getVendorId())) {
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
        if ($allCategories = self::getCategoryDataSource()->getAll(self::getVendorId())) {
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
        if ($category = self::getCategoryDataSource()->getCategory($categoryId, self::getVendorId())) {
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
        return self::getCategoryDataSource()->getDescendants($categoryId, self::getVendorId());
    }

    /**
     * @param $categoryId
     * @return array
     * @throws \Exception
     */
    public function getChildren($categoryId): array
    {
        self::setCategoryId($categoryId);
        return self::getCategoryDataSource()->getChildren($categoryId, self::getVendorId());
    }

    /**
     * @param $categoryId
     * @return array
     * @throws \Exception
     */
    public function getParent($categoryId): array
    {
        self::setCategoryId($categoryId);
        return self::getCategoryDataSource()->getParent($categoryId, self::getVendorId());
    }

    /**
     * @param $categoryId
     * @return array
     * @throws \Exception
     */
    public function getParents($categoryId): array
    {
        self::setCategoryId($categoryId);
        return self::getCategoryDataSource()->getParents($categoryId, self::getVendorId());
    }

    /**
     * Create category
     * @param array $data
     * @return array
     * @throws \Exception
     */
    public function create(array $data): array
    {
        $category = self::getCategoryRepository()->create($data);
        self::getCategoryCache()->invalidateCache();
        return $category->toApiArray();
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
        $category = self::getCategoryRepository()->update($categoryId, self::getVendorId(), $data);
        self::getCategoryCache()->invalidateCache();
        return $category->toApiArray();
    }

    /**
     * @param $categoryId
     * @throws \Exception
     */
    public function delete($categoryId): void
    {
        if (self::getCategoryRepository()->delete($categoryId, self::getVendorId())) {
            self::getCategoryCache()->invalidateCache();
        } else {
            throw new \Exception('Category not found or maybe deleted', 404);
        }
    }
}
