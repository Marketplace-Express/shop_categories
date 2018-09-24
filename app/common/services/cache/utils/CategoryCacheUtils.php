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
    public $array;

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
     * Get category descendants
     * @param \RecursiveIteratorIterator $iterator
     * @param string $categoryId
     * @return array
     */
    private function getDescendants(\RecursiveIteratorIterator $iterator, string $categoryId)
    {
        while ($iterator->valid()) {
            if ($iterator->key() == 'categoryId' && $iterator->current() == $categoryId) {
                return (array) $iterator->getInnerIterator();
            }

            $iterator->next();
        }

        return [];
    }

    private function getChildren(\RecursiveIteratorIterator $iterator, string $categoryId): array
    {
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
        }

        return $category;
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
     * TODO: need to be enhanced more, it consumes a lot of memory
     * @param \RecursiveIteratorIterator $iterator
     * @param string $categoryId
     * @param bool $prevLevel
     * @return array
     */
    private function getParents(\RecursiveIteratorIterator $iterator, string $categoryId, bool $prevLevel = false)
    {
        // Initialization
        $parents = [];

        // Get most parent category Id
        while ($iterator->valid()) {
            if ($iterator->key() == 'categoryId' && $iterator->current() == $categoryId) {

                // Get category details as array
                $category = (array)$iterator->getInnerIterator();

                // Check if category is root node
                if ($category['lft'] == 1) {
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

    private function findById(\RecursiveIteratorIterator $iterator, string $categoryId)
    {
        $category = [];
        while ($iterator->valid()) {
            if ($iterator->key() == 'categoryId' && $iterator->current() == $categoryId) {
                return (array) $iterator->getInnerIterator();
            }

            $iterator->next();
        }
        $category = array_slice($category, 0, count($category) - 1);
        return $category;
    }

    /** Returns the first level items from multidimensional array
     * @return array
     */
    public function getRoots(): array
    {
        foreach ($this->array as &$item) {
            $item = array_slice($item, 0, count($item) - 1);
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

    /**
     * Return category details, including/excluding children
     * @param string $categoryId
     * @param string $operation
     * @return array|bool
     */
    public function getCategory(string $categoryId, string $operation): array
    {
        $iterator = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($this->array));
        switch ($operation) {
            case 'getDescendants':
                return $this->getDescendants($iterator, $categoryId);
                break;
            case 'getChildren':
                return $this->getChildren($iterator, $categoryId);
                break;
            case 'getParents':
                return $this->getParents($iterator, $categoryId);
                break;
            case 'getParent':
                return $this->getParents($iterator, $categoryId, true);
                break;
            case 'getCategory':
                return $this->findById($iterator, $categoryId);
                break;
            default:
                return [];
        }
    }
}