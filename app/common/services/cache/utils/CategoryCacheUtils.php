<?php
/**
 * User: Wajdi Jurry
 * Date: 24/08/18
 * Time: 06:52 م
 */

namespace app\common\services\cache\utils;

class CategoryCacheUtils
{
    const SUB_ITEMS_SLUG = 'children';

    /**
     * @var array $array
     */
    private $array;

    /**
     * @param array $array
     * @return CategoryCacheUtils
     */
    public function setArray(array $array): self
    {
        $this->array = $array;
        return $this;
    }

    /**
     * @return \RecursiveIteratorIterator|\ArrayIterator
     */
    private function getIterator()
    {
        return new \RecursiveIteratorIterator(new \RecursiveArrayIterator($this->array));
    }

    private function toTree(array $array): array
    {
        $tree = [];
        foreach ($array as $key => $item) {
            unset($array[$key]);
            $item[self::SUB_ITEMS_SLUG] = (!empty($children = $this->toTree($array))) ? $children : null;
            $tree[] = $item;
        }
        return array_slice($tree, 0, 1);
    }

    /**
     * Get category descendants
     * @param string $categoryId
     * @return array
     */
    public function getDescendants(string $categoryId)
    {
        $iterator = $this->getIterator();
        while ($iterator->valid()) {
            if ($iterator->key() == 'categoryId' && $iterator->current() == $categoryId) {
                return $iterator->offsetGet('children');
            }
            $iterator->next();
        }
        return [];
    }

    /**
     * Get category children
     * @param string $categoryId
     * @return array
     */
    public function getChildren(string $categoryId): array
    {
        $iterator = $this->getIterator();
        $category = [];
        while ($iterator->valid()) {
            if ($iterator->key() == 'categoryId' && $iterator->current() == $categoryId) {
                $category = (array) $iterator->getSubIterator();
                break;
            }

            $iterator->next();
        }

        if (array_key_exists(self::SUB_ITEMS_SLUG, $category)) {
            $keyToRemove = array_flip([self::SUB_ITEMS_SLUG]);
            $category[self::SUB_ITEMS_SLUG] = array_map(function ($item) use($keyToRemove) {
                // This will remove children from subset categories
                return array_diff_key($item, $keyToRemove);
            }, $category[self::SUB_ITEMS_SLUG]);
            return $category[self::SUB_ITEMS_SLUG];
        }
        return [];
    }

    /**
     * TODO: need to be enhanced more, it consumes a lot of memory
     * Get category parent(s)
     * @param string $categoryId
     * @param bool $prevLevel
     * @return array
     */
    public function getParents(string $categoryId, bool $prevLevel = false)
    {
        $iterator = $this->getIterator();

        // Initialization
        $parents = [];

        // Get most parent category Id
        while ($iterator->valid()) {
            if ($iterator->key() == 'categoryId' && $iterator->current() == $categoryId) {

                // Get category details as array
                $category = (array)$iterator->getInnerIterator();

                // Check if category is root node
                if (array_key_exists('lft', $category) && $category['lft'] == 1) {
                    return [];
                }

                // Step back the iterator
                if ($prevLevel) {
                    $depth = 3;
                } else {
                    $depth = $iterator->getDepth();
                }

                for ($i = 1; $i < $depth; $i++) {
                    $currentArr = (array) $iterator->getSubIterator($iterator->getDepth() - $i);
                    if (array_key_exists('categoryId', $currentArr)) {
                        $parents[] = array_slice($currentArr, 0, count($currentArr) - 1);
                    }
                }

                // Stop looping
                break;
            }
            // next iterator
            $iterator->next();
        }

        // Order categories recursively
        if (count($parents) > 1) {
            $parents = $this->toTree(array_reverse($parents));
        }

        return $parents;
    }

    /**
     * Get category by id
     * @param string $categoryId
     * @return array
     */
    public function getCategory(string $categoryId)
    {
        $iterator = $this->getIterator();
        $category = [];
        while ($iterator->valid()) {
            if ($iterator->key() == 'categoryId' && $iterator->current() == $categoryId) {
                $category = (array) $iterator->getInnerIterator();
                break;
            }

            $iterator->next();
        }
        //unset($category[self::SUB_ITEMS_SLUG]);
        return $category;
    }

    /**
     * @param array $categoriesIds
     * @return array
     */
    public function getByIds(array $categoriesIds)
    {
        $iterator = $this->getIterator();
        $categories = [];
        while ($iterator->valid()) {
            if ($iterator->key() == 'categoryId' && in_array($iterator->current(), $categoriesIds)) {
                $categories[] = (array) $iterator->getInnerIterator();
            }
            if (count($categories) == count($categoriesIds)) {
                // break the loop if all categories with specified ids fetched
                break;
            }
            $iterator->next();
        }
        return $categories;
    }

    /** Returns roots
     * @return array
     */
    public function getRoots(): array
    {
        foreach ($this->array as &$item) {
            unset($item[self::SUB_ITEMS_SLUG]);
        }
        return $this->array;
    }

    /**
     * Return all categories
     * @return array
     */
    public function getAll(): array
    {
        return $this->array;
    }
}
