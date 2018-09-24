<?php
/**
 * User: Wajdi Jurry
 * Date: 20/08/18
 * Time: 05:29 م
 */

namespace Shop_categories\Services;

use Phalcon\Mvc\Model\Behavior\NestedSet;
use Phalcon\Mvc\ModelInterface;
use Shop_categories\Repositories\CategoryRepository;

class CategoryService extends BaseService
{
    /**
     * @return array
     * @throws \Exception
     */
    public function getRoots(): array
    {
        if ($roots = self::getCacheService()->getRoots()) {
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
        if ($allCategories = self::getCacheService()->getAll()) {
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

        if ($category = self::getCacheService()->getCategory()) {
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
        return self::getRepository()->descendants($categoryId);
        self::setCategoryId($categoryId);

        if ($descendants = self::getCacheService()->getDescendants()) {
            return $descendants;
        }

        throw new \Exception('Category not found', 404);
    }

    /**
     * @param $categoryId
     * @return array
     * @throws \Exception
     */
    public function getChildren($categoryId): array
    {
        self::setCategoryId($categoryId);

        if ($children = self::getCacheService()->getChildren()) {
            return $children;
        }

        throw new \Exception('Category not found', 404);
    }

    /**
     * @param $categoryId
     * @return array
     * @throws \Exception
     */
    public function getParent($categoryId): array
    {
        self::setCategoryId($categoryId);

        if ($parent = self::getCacheService()->getParent()) {
            return $parent;
        }

        throw new \Exception('Category not found', 404);
    }

    /**
     * @param $categoryId
     * @return array
     * @throws \Exception
     */
    public function getParents($categoryId): array
    {
        self::setCategoryId($categoryId);

        if ($parents = self::getCacheService()->getParents()) {
            return $parents;
        }

        throw new \Exception('Category not found', 404);
    }

    /**
     * Create category object from array
     * @param array $data
     * @return CategoryRepository
     * @throws \Exception
     */
    private function categoryObject(array $data)
    {
        $category = new CategoryRepository();
        foreach ($data as $column => $value) {
            $category->{$column} = $value;
        }
        return $category;
    }

    /**
     * @param $categoryId
     * @return \Phalcon\Mvc\Model\ResultInterface|CategoryRepository
     * @throws \Exception
     */
    public function getCategoryFromRepository($categoryId)
    {
        if ($category = self::getRepository()::findById($categoryId, self::getVendorId())) {
            return $category;
        }

        throw new \Exception('Category not found', 404);
    }

    /**
     * Create/Update category
     * @param array $data
     * @param CategoryRepository $category
     * @return CategoryRepository
     * @throws \Exception
     */
    public function save(array $data, $category = null): CategoryRepository
    {
        if (!$category instanceof CategoryRepository) {
            if (!$data) {
                throw new \Exception('No data to be saved', 400);
            }
            $category = $this->categoryObject($data);
        }

        if ($category->save($data)) {
            $this->invalidateCache();
            return self::getCategoryFromRepository($category->getCategoryId());
        }

        throw new \Exception(implode($category->getMessages()), 500);
    }

    /**
     * @param $categoryId
     * @return bool
     * @throws \Exception
     */
    public function delete($categoryId): bool
    {
        $category = self::getRepository()::findById($categoryId, self::getVendorId());
        if ($category) {
            $this->invalidateCache();
            return $category->delete();
        }

        throw new \Exception('Category not found or maybe deleted', 404);
    }

    public function invalidateCache()
    {
        self::getCacheService()->invalidateCache();
    }
}
