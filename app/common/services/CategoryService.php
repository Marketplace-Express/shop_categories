<?php
/**
 * User: Wajdi Jurry
 * Date: 20/08/18
 * Time: 05:29 م
 */

namespace Shop_categories\Services;

use RedisException;
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
            throw new \Exception($exception->getMessage() ?: 'No data source available for categories');
        }
    }

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
        return self::getCategoryDataSource()->getRoots(self::getVendorId());
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getAll(): array
    {
        return self::getCategoryDataSource()->getAll(self::getVendorId());
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
        $category = self::getCategoryRepository()->create($data)->toApiArray();
        try {
            self::getCategoryCache()->invalidateCache();
            self::getCategoryCache()->indexCategory($category);
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
        $category = self::getCategoryRepository()->update($categoryId, self::getVendorId(), $data);
        try {
            self::getCategoryCache()->invalidateCache();
        } catch (\RedisException $exception) {
            // do nothing
        }
        return $category->toApiArray();
    }

    /**
     * @param $categoryId
     * @throws \Exception
     */
    public function delete($categoryId): void
    {
        if (self::getCategoryRepository()->delete($categoryId, self::getVendorId())) {
            try {
                self::getCategoryCache()->invalidateCache();
            } catch (\RedisException $exception) {
                // do nothing
            }
        } else {
            throw new \Exception('Category not found or maybe deleted', 404);
        }
    }
}
