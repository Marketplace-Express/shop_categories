<?php
/**
 * Created by PhpStorm.
 * User: wajdi
 * Date: 27/07/18
 * Time: 06:13 م
 */

namespace Shop_categories\Repositories;

use Phalcon\Mvc\Model\Behavior\NestedSet;
use Phalcon\Mvc\Model\ResultInterface;
use Shop_categories\Models\Category;

class CategoryRepository extends Category
{

    /**
     * Find node ny ID
     * @param string $categoryId
     * @return \Phalcon\Mvc\Model\ResultInterface
     */
    public static function findById(string $categoryId)
    {
        return self::findFirst([
            'conditions' => 'categoryId = :id: AND isDeleted = 0',
            'bind' => ['id' => $categoryId]
        ]);
    }

    /**
     * Get All Roots
     * @return bool|\Phalcon\Mvc\Model\Resultset|\Phalcon\Mvc\Model\ResultSetInterface|CategoryRepository|CategoryRepository[]
     */
    public static function getRoots()
    {
        return self::find('lft = 1 AND isDeleted = 0');
    }

    /**
     * Getting all children of a node
     * @param string $categoryId
     * @return bool|\Phalcon\Mvc\Model\ResultsetInterface
     */
    public static function getChildren(string $categoryId)
    {
        /**
         * @var NestedSet $category
         */
        $category = self::findFirst([
            'conditions' => 'categoryId = :id: AND isDeleted = 0',
            'bind' => ['id' => $categoryId]
        ]);

        if ($category) {
            return $category->children(null, true);
        }

        return false;
    }

    /**
     * Getting all descendants of node
     * @param string $categoryId
     * @return bool|NestedSet
     */
    public static function getDescendants(string $categoryId)
    {
        /**
         * @var NestedSet $category
         */
        $category = self::findFirst([
            'conditions' => 'categoryId = :id: AND isDeleted = 0',
            'bind' => ['id' => $categoryId]
        ]);

        if ($category) {
            return $category->descendants(null, true);
        }

        return false;
    }

    /**
     * Get ancestor of node
     * @param string $categoryId
     * @param null $depth
     * @return bool|NestedSet
     */
    public static function getParents(string $categoryId, $depth = null)
    {
        /**
         * @var NestedSet $category
         */
        $category = self::findFirst([
            'conditions' => 'categoryId = :id: AND isDeleted = 0',
            'bind' => ['id' => $categoryId]
        ]);

        if ($category) {
            return $category->ancestors($depth);
        }

        return false;
    }
}