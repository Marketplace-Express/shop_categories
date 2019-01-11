<?php
/**
 * User: Wajdi Jurry
 * Date: 24/08/18
 * Time: 06:52 م
 */

namespace Shop_categories\Services\Cache\Utils;


class CategoryCacheUtils
{
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
     * @return \RecursiveIteratorIterator
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
            $item['children'] = (!empty($children = $this->toTree($array))) ? $children : null;
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
                return (array) $iterator->getInnerIterator();
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

        if (array_key_exists('children', $category)) {
            $keyToRemove = array_flip(['children']);
            $category['children'] = array_map(function ($item) use($keyToRemove) {
                // This will remove children from subset categories
                return array_diff_key($item, $keyToRemove);
            }, $category['children']);
            return $category['children'];
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
        unset($category['children']);
        return $category;
    }

    /** Returns roots
     * @return array
     */
    public function getRoots(): array
    {
        foreach ($this->array as &$item) {
            unset($item['children']);
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