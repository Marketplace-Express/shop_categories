<?php
/**
 * User: Wajdi Jurry
 * Date: 20/08/18
 * Time: 05:29 م
 */

namespace Shop_categories\Services;

use Shop_categories\Repositories\CategoryRepository;

class CategoryService extends BaseService
{
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
     * Check if vendor has a specific category
     * @param string $categoryId
     * @return \Phalcon\Mvc\Model\ResultInterface|CategoryRepository
     * @throws \Exception
     */
    private function categoryVendorCheck(string $categoryId)
    {
        return $this->getCategoryFromRepository($categoryId);
    }

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
        self::setCategoryId($categoryId);

        if ($descendants = self::getCacheService()->getDescendants()) {
            return $descendants;
        }

        throw new \Exception('Category not found or maybe deleted', 404);
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
     * @param $categoryId
     * @return \Phalcon\Mvc\Model\ResultInterface|CategoryRepository
     * @throws \Exception
     */
    public function getCategoryFromRepository($categoryId)
    {
        if ($category = self::getRepository()::findById($categoryId, self::getVendorId())) {
            return $category;
        }

        throw new \Exception('Category not found or maybe deleted', 404);
    }

    /**
     * Create category
     * @param array $data
     * @return array
     * @throws \Exception
     */
    public function create(array $data): array
    {
        if (!empty($data['categoryParentId'])) {
            $this->categoryVendorCheck($data['categoryParentId']);
        }

        $category = $this->categoryObject($data);

        $data['vendorId'] = self::getVendorId();

        if ($category->save($data)) {
            $this->invalidateCache();
            return self::getCategoryFromRepository($category->getCategoryId())->toArray();
        }

        throw new \Exception(implode($category->getMessages()), 500);
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
        $category = $this->getCategoryFromRepository($categoryId);

        if (!empty($data['categoryParentId'])) {
            $this->categoryVendorCheck($data['categoryParentId']);
        }
        if ($category->save($data)) {
            $this->invalidateCache();
            return $category->toArray();
        }
        throw new \Exception('Category could not be updated', 500);
    }

    /**
     * @param $categoryId
     * @return bool
     * @throws \Exception
     */
    public function delete($categoryId): bool
    {
        $this->categoryVendorCheck($categoryId);
        if (!self::getRepository()->cascadeDelete($categoryId)) {
            return false;
        }
        $this->invalidateCache();
        return true;
    }

    public function invalidateCache()
    {
        self::getCacheService()->invalidateCache();
    }
}
