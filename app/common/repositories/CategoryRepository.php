<?php
/**
 * User: Wajdi Jurry
 * Date: 27/07/18
 * Time: 06:13 م
 */

namespace Shop_categories\Repositories;

use Phalcon\Mvc\Model\ResultInterface;
use Shop_categories\Models\Behaviors\AdjacencyListModelBehavior;
use Shop_categories\Models\Category;

class CategoryRepository extends Category
{

    private static $resultSet;

    /**
     * Find category by ID
     * @param string $categoryId
     * @param string $vendorId
     * @return ResultInterface
     */
    public static function findById(string $categoryId, string $vendorId)
    {
        return self::findFirst([
            'conditions' => 'categoryId = :id: AND vendorId = :vendorId: AND isDeleted = 0',
            'bind' => ['id' => $categoryId, 'vendorId' => $vendorId]
        ]);
    }

    /**
     * Find multiple categories
     * @param array $categoriesIds
     * @return \Phalcon\Mvc\Model\ResultsetInterface
     */
    public static function findAllByIds(array $categoriesIds)
    {
        $result = self::find([
            'conditions' => 'categoryId IN ({categoriesIds:array})',
            'bind' => ['categoriesIds' => $categoriesIds]
        ]);

        self::$resultSet = $result;

        return $result;
    }

    /**
     * Get All Roots
     * @param string $vendorId
     * @return bool|\Phalcon\Mvc\Model\Resultset|\Phalcon\Mvc\Model\ResultSetInterface|CategoryRepository|CategoryRepository[]
     */
    public static function getRoots(string $vendorId)
    {
        $result = self::find([
            'conditions' => 'categoryParentId IS NULL AND vendorId = :vendorId: AND isDeleted = 0',
            'bind' => ['vendorId' => $vendorId]
        ]);

        return $result;
    }

    /**
     * Getting all children of a node
     * @param string $categoryId
     * @param string $vendorId
     * @return \Phalcon\Mvc\Model
     */
    public static function getChildren(string $categoryId, string $vendorId)
    {
        $children = self::findFirst([
            'conditions' => 'categoryParentId = :id: AND vendorID = :vendorId: AND isDeleted = 0',
            'bind' => ['id' => $categoryId, 'vendorId' => $vendorId]
        ]);

        self::$resultSet = $children;

        return $children;
    }

    /**
     * Getting all descendants of node
     * @param string $categoryId
     * @param string $vendorId
     * @return array|bool
     */
    public function getDescendants(string $categoryId, string  $vendorId)
    {
        return $this->descendants(['categoryId' => $categoryId, 'vendorId' => $vendorId]);
    }

    /**
     * Get ancestor of node
     * @param string $categoryId
     * @param string $vendorId
     * @param null $depth
     * @return array|bool
     */
    public function getParents(string $categoryId, string $vendorId)
    {
        return $this->parents(['categoryId' => $categoryId, 'vendorId' => $vendorId]);
    }

    /**
     * Gett all categories related to vendor
     * @param string $vendorId
     * @return array
     * @throws \Exception
     */
    public function getAll(string $vendorId)
    {
        $roots = self::find([
            'conditions' => 'vendorId = :vendorId: AND isDeleted = false',
            'bind' => ['vendorId' => $vendorId]
        ]);

        return $this->recursive($roots->toArray());
    }
}