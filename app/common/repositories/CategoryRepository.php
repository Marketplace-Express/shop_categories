<?php
/**
 * User: Wajdi Jurry
 * Date: 27/07/18
 * Time: 06:13 م
 */

namespace app\common\repositories;

use app\common\exceptions\NotFoundException;
use app\common\exceptions\OperationFailedException;
use app\common\dbTools\enums\SchemaQueryOperatorsEnum;
use app\common\exceptions\ArrayOfStringsException;
use app\common\interfaces\CategoryDataSourceInterface;
use app\common\models\behaviors\AdjacencyListModelBehavior;
use app\common\models\Category;

class CategoryRepository implements CategoryDataSourceInterface
{
    /**
     * @return CategoryRepository
     */
    static public function getInstance()
    {
        return new self;
    }

    /**
     * @param bool $new
     * @return Category|AdjacencyListModelBehavior
     */
    public static function getModel(bool $new = false): Category
    {
        return Category::model($new);
    }

    /**
     * Find category by ID
     * @param string $categoryId
     * @param string $storeId
     * @return Category
     * @throws \Exception
     */
    public static function findById(string $categoryId, string $storeId)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $result = self::getModel()->getItems([
            'id' => [SchemaQueryOperatorsEnum::OP_EQUALS => $categoryId],
            'storeId' => [SchemaQueryOperatorsEnum::OP_EQUALS => $storeId]
        ]);

        if (count($result)) {
            return array_shift($result);
        }

        throw new \Exception('Category not found or maybe deleted', 404);
    }

    /**
     * Find multiple categories
     * @param array $categoriesIds
     * @param string $storeId
     * @return \Phalcon\Mvc\Model\ResultsetInterface
     * @throws \Exception
     */
    public static function findAllByIds(array $categoriesIds, string $storeId)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $result = self::getModel()->getItems([
            'id' => [SchemaQueryOperatorsEnum::OP_IN => [$categoriesIds]],
            'storeId' => [SchemaQueryOperatorsEnum::OP_EQUALS => $storeId]
        ]);

        if (count($result)) {
            return $result;
        }

        throw new \Exception('No categories found', 404);

    }

    public function getColumnsForCategory(string $categoryId, string $storeId, array $columns = [])
    {
        $category = self::getModel(true)::findFirst([
            'columns' => implode(',', $columns),
            'conditions' => 'id = :categoryId: AND storeId = :storeId: AND isDeleted = false',
            'bind' => [
                'categoryId' => $categoryId,
                'storeId' => $storeId
            ]
        ]);
        if (!$category) {
            throw new NotFoundException('Category not found or maybe deleted');
        }
        return $category;
    }

    /**
     * Get category by Id
     * @param string $categoryId
     * @param string $storeId
     * @return Category
     * @throws NotFoundException
     * @throws \Exception
     */
    public function getCategory(string $categoryId, string $storeId): ?Category
    {
        $category = self::findById($categoryId, $storeId);
        if (!$category) {
            throw new NotFoundException('Category not found or maybe deleted');
        }
        return $category;
    }

    /**
     * Get All Roots
     * @param string $storeId
     * @return array
     * @throws \Exception
     */
    public function getRoots(string $storeId): array
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $roots = $this::getModel()->roots([
            'storeId' => [SchemaQueryOperatorsEnum::OP_EQUALS => $storeId]
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
     * @param string $storeId
     * @return array
     * @throws \Exception
     */
    public function getChildren(string $categoryId, string $storeId): array
    {
        /** @var Category[] $children */
        /** @noinspection PhpUndefinedMethodInspection */
        $children = self::getModel()->children($categoryId, [
            'storeId' => [SchemaQueryOperatorsEnum::OP_EQUALS => $storeId]
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
     * @param string $storeId
     * @return array
     *
     * @throws \Exception
     */
    public function getDescendants(string $categoryId, string $storeId): array
    {
        /** @var Category[] $descendants */
        /** @noinspection PhpUndefinedMethodInspection */
        $descendants = self::getModel()->descendants($categoryId, [
            'storeId' => [SchemaQueryOperatorsEnum::OP_EQUALS => $storeId]
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
     * @param string $storeId
     * @return array|bool
     *
     * @throws \Exception
     */
    public function getParents(string $categoryId, string $storeId): array
    {
        /** @var Category[] $parents */
        /** @noinspection PhpUndefinedMethodInspection */
        $parents = self::getModel()->parents($categoryId, [
            'storeId' => [SchemaQueryOperatorsEnum::OP_EQUALS => $storeId]
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
     * @param string $storeId
     * @return Category
     *
     * @throws \Exception
     */
    public function getParent(string $categoryId, string $storeId): Category
    {
        /** @var Category $parent */
        $parent = self::getModel()->parents($categoryId, [
            'storeId' => [SchemaQueryOperatorsEnum::OP_EQUALS => $storeId]
        ], true);

        if (!$parent) {
            throw new NotFoundException('No parent category');
        }

        return $parent;
    }

    /**
     * Get all categories related to store
     * @param string $storeId
     * @return array
     * @throws \Exception
     */
    public function getAll(string $storeId): array
    {
        /** @var Category[] $categories */
        $categories = $this::getModel()->getItems([
            'storeId' => [SchemaQueryOperatorsEnum::OP_EQUALS => $storeId]
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
     * Create new category
     *
     * @param array $data
     * @return Category
     * @throws ArrayOfStringsException
     * @throws \Exception
     */
    public function create(array $data): Category
    {
        $category = self::getModel(true);
        if (!empty($data['parentId'])) {
            $parent = $this->getColumnsForCategory($data['parentId'], $data['storeId'], ['depth']);
            $data['depth'] = $parent->depth + 1;
        }
        if (!$category->create($data, Category::CREATE_WHITE_LIST)) {
            throw new ArrayOfStringsException($category->getMessages(), 400);
        }
        return $category;
    }

    /**
     * Update category
     *
     * @param string $categoryId
     * @param string $storeId
     * @param array $data
     * @return Category
     * @throws \Exception
     */
    public function update(string $categoryId, string $storeId, array $data)
    {
        $category = self::findById($categoryId, $storeId);
        if (!empty($data['parentId']) && $category->parentId !== $data['parentId']) {
            $parentCategory = $this->getColumnsForCategory($data['parentId'], $storeId, ['depth']);
            $data['depth'] += $parentCategory->depth;
        }
        if (!$category->update($data, Category::UPDATE_WHITE_LIST)) {
            throw new ArrayOfStringsException($category->getMessages(), 400);
        }
        return $category;
    }

    /**
     * Delete category
     *
     * @param string $categoryId
     * @param string $storeId
     * @return bool
     *
     * @throws OperationFailedException
     * @throws NotFoundException
     *
     */
    public function delete(string $categoryId, string $storeId)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return self::getModel()->deleteCascade($categoryId, [
            'storeId' => [SchemaQueryOperatorsEnum::OP_EQUALS => $storeId]
        ]);
    }
}
