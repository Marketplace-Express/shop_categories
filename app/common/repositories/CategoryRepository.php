<?php
/**
 * User: Wajdi Jurry
 * Date: 27/07/18
 * Time: 06:13 م
 */

namespace Shop_categories\Repositories;

use Phalcon\Mvc\Model\Resultset;
use Shop_categories\DBTools\Enums\QueryOperatorsEnum;
use Shop_categories\Exceptions\ArrayOfStringsException;
use Shop_categories\Models\Category;

class CategoryRepository
{
    /**
     * @param bool $new
     * @return Category
     */
    public static function getModel(bool $new = false): Category
    {
        return Category::model($new);
    }

    /**
     * Find category by ID
     * @param string $categoryId
     * @param string $vendorId
     * @return Category
     * @throws \Exception
     */
    public static function findById(string $categoryId, string $vendorId)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $result = self::getModel()->getItems([
            'categoryId' => [QueryOperatorsEnum::OP_EQUALS => $categoryId],
            'categoryVendorId' => [QueryOperatorsEnum::OP_EQUALS => $vendorId]
        ]);

        if (count($result)) {
            return array_shift($result);
        }

        throw new \Exception('Category not found or maybe deleted', 404);
    }

    /**
     * Find multiple categories
     * @param array $categoriesIds
     * @param string $vendorId
     * @return \Phalcon\Mvc\Model\ResultsetInterface
     * @throws \Exception
     */
    public static function findAllByIds(array $categoriesIds, string $vendorId)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $result = self::getModel()->getItems([
            'categoryId' => [QueryOperatorsEnum::OP_IN => [$categoriesIds]],
            'categoryVendorId' => [QueryOperatorsEnum::OP_EQUALS => $vendorId]
        ]);

        if (count($result)) {
            return $result;
        }

        throw new \Exception('No categories found', 404);

    }

    /**
     * Get category by Id
     * @param string $categoryId
     * @param string $vendorId
     * @return array
     * @throws \Exception
     */
    public function getCategory(string $categoryId, string $vendorId)
    {
        return self::findById($categoryId, $vendorId)->toApiArray();
    }

    /**
     * Get All Roots
     * @param string $vendorId
     * @return array
     * @throws \Exception
     */
    public function getRoots(string $vendorId): array
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $roots = $this::getModel()->roots([
            'categoryVendorId' => [QueryOperatorsEnum::OP_EQUALS => $vendorId]
        ]);
        $result = [];
        if (count($roots)) {
            /** @var Category $root */
            foreach ($roots as $root) {
                $result[] = $root->toApiArray();
            }
        }
        return $result;
    }

    /**
     * Getting all children of a node
     * @param string $categoryId
     * @param string $vendorId
     * @return array
     * @throws \Exception
     */
    public static function getChildren(string $categoryId, string $vendorId)
    {
        /** @var Category[] $children */
        /** @noinspection PhpUndefinedMethodInspection */
        $children = self::getModel()->children($categoryId, [
            'categoryVendorId' => [QueryOperatorsEnum::OP_EQUALS => $vendorId]
        ]);

        $result = [];
        if (count($children)) {
            foreach ($children as $child) {
                $result[] = $child->toApiArray();
            }
        }
        return $result;
    }

    /**
     * Getting all descendants of node
     * @param string $categoryId
     * @param string $vendorId
     * @return array
     */
    public function getDescendants(string $categoryId, string $vendorId)
    {
        /** @var Category[] $descendants */
        /** @noinspection PhpUndefinedMethodInspection */
        $descendants = self::getModel()->descendants($categoryId, [
            'categoryVendorId' => [QueryOperatorsEnum::OP_EQUALS => $vendorId]
        ]);

        $result = [];
        foreach ($descendants as $descendant) {
            $result[] = $descendant->toApiArray();
        }
        return $result;
    }

    /**
     * Get ancestor of node
     * @param string $categoryId
     * @param string $vendorId
     * @return array|bool
     */
    public function getParents(string $categoryId, string $vendorId)
    {
        /** @var Category[] $parents */
        /** @noinspection PhpUndefinedMethodInspection */
        $parents = self::getModel()->parents($categoryId, [
            'categoryVendorId' => [QueryOperatorsEnum::OP_EQUALS => $vendorId]
        ]);

        $result = [];
        if (count($parents)) {
            foreach ($parents as $parent) {
                $result[] = $parent->toApiArray();
            }
        }
        return $result;
    }

    /**
     * Get ancestor of node
     * @param string $categoryId
     * @param string $vendorId
     * @return array|bool
     */
    public function getParent(string $categoryId, string $vendorId)
    {
        /** @var Category[] $parent */
        /** @noinspection PhpUndefinedMethodInspection */
        $parent = self::getModel()->parents($categoryId, [
            'categoryVendorId' => [QueryOperatorsEnum::OP_EQUALS => $vendorId]
        ], true);

        return array_map(function($category) {
            /** @var Category $category */
            return $category->toApiArray();
            }, $parent);
    }

    /**
     * Get all categories related to vendor
     * @param string $vendorId
     * @return array
     * @throws \Exception
     */
    public function getAll(string $vendorId)
    {
        /** @var Category[] $categories */
        /** @noinspection PhpUndefinedMethodInspection */
        $categories = $this::getModel()->getItems([
            'categoryVendorId' => [QueryOperatorsEnum::OP_EQUALS => $vendorId]
        ]);

        $result = [];
        if (count($categories)) {
            foreach ($categories as $category) {
                $result[] = $category->toApiArray();
            }
        }
        return $result;
    }

    /**
     * @param array $data
     * @return Category
     * @throws ArrayOfStringsException
     */
    public function create(array $data): Category
    {
        $category = self::getModel(true)->setAttributes($data);
        if (!$category->save()) {
            throw new ArrayOfStringsException($category->getMessages(), 400);
        }
        return $category;
    }

    /**
     * @param string $categoryId
     * @param string $vendorId
     * @param array $data
     * @return Category
     * @throws \Exception
     */
    public function update(string $categoryId, string $vendorId, array $data)
    {
        $category = self::findById($categoryId, $vendorId);
        $category->setAttributes($data);
        if (!$category->update()) {
            throw new ArrayOfStringsException($category->getMessages(), 400);
        }
        return $category;
    }

    public function delete(string $categoryId, string $vendorId)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return self::getModel()->deleteCascade($categoryId, [
            'categoryVendorId' => [QueryOperatorsEnum::OP_EQUALS => $vendorId]
        ]);
    }
}