<?php
/**
 * User: Wajdi Jurry
 * Date: 14/09/18
 * Time: 03:01 ص
 */

namespace Shop_categories\Helpers;

class AdjacencyListModelHelper
{
    private $parentIdAttribute;
    private $itemIdAttribute;
    private $subItemsSlug;
    private $noParentValue;

    private $unordered;
    private $array;

    public function __construct(array $array, array $params)
    {
        $this->array = $array;

        $this->itemIdAttribute = $params['itemIdAttribute'];
        $this->parentIdAttribute = $params['parentIdAttribute'];
        $this->subItemsSlug = $params['subItemsSlug'];
        $this->noParentValue = $params['noParentValue'];
    }

    /**
     * Shifting parent items to the top of array
     * @param array $array
     * @return array
     * @throws \Exception
     */
    private function reorder(array $array)
    {
        if (count($array)) {
            return array_intersect_key(
                $array,
                array_flip(
                    array_keys(
                        array_column($array, $this->parentIdAttribute),
                        null
                    )
                )
            ) + $array;
        }

        throw new \Exception('Empty array');
    }

    /**
     * Handling deep items (level max. than 2)
     * @param array $array
     * @param $subArray
     * @param $itemKey
     * @return void
     */
    private function handle(array &$array, $subArray, $itemKey)
    {
        foreach ($array as $key => &$item) {
            if (array_key_exists($this->itemIdAttribute, $item) && $item[$this->itemIdAttribute] == $subArray[$this->parentIdAttribute]) {
                $item[$this->subItemsSlug][] = $subArray;
                unset($this->unordered[$itemKey]);
                break;
            } elseif (is_array($item[$this->subItemsSlug])) {
                $this->handle($item[$this->subItemsSlug], $subArray, $itemKey);
            }
        }
    }

    /**
     * Extract parent items from array
     * Important: items with nonexistent parents in array will not be considered
     * @return array
     * @throws \Exception
     */
    private function createSubParentsArray()
    {
        $subParents = array_unique(array_column($this->unordered, $this->parentIdAttribute));
        $subIds = array_unique(array_column($this->unordered, $this->itemIdAttribute));
        $existsParents = array_intersect($subParents, $subIds);
        return array_values(array_filter(array_map(function($item, $key) use ($existsParents) {
            if (in_array($item[$this->itemIdAttribute], $existsParents)) {
                unset($this->unordered[$key]);
                return $item;
            }
            return false;
        }, $this->unordered, array_keys($this->unordered))));
    }

    /**
     * Creating recursive from one dimension array
     * @param array $array
     * @return array
     * @throws \Exception
     */
    private function prepare(array $array)
    {
        if (count($array) <= 1) {
            return $array;
        }

        $tree = [];
        $array = $this->reorder($array);

        do {
            foreach ($array as $key => $item) {
                $item[$this->subItemsSlug] = null;
                if (empty($item[$this->parentIdAttribute]) || $item[$this->itemIdAttribute] == $item[$this->parentIdAttribute]) {
                    $tree[$item[$this->itemIdAttribute]] = $item;
                    unset($array[$key]);
                    continue;
                }
                if (array_key_exists($item[$this->parentIdAttribute], $tree)) {
                    $tree[$item[$this->parentIdAttribute]][$this->subItemsSlug][] = $item;
                    unset($array[$key]);
                    continue;
                }
                $this->unordered[] = $item;
                unset($array[$key]);
                break;
            }
        } while (count($array));

        if (empty($tree)) {
            $tree = $this->createSubParentsArray();
        }

        do {
            foreach ($this->unordered as $key => $item) {
                $this->handle($tree, $item, $key);
            }
        } while (count($this->unordered));

        return $tree;
    }

    /**
     * Get first sub level items of a node
     * @param $itemId
     * @return array
     * @throws \Exception
     */
    public function children($itemId)
    {
        $item = null;
        $this->prepare($this->array);
        $iterator = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($this->array));
        while ($iterator->valid()) {
            if ($iterator->key() == $this->itemIdAttribute && $iterator->current() == $itemId) {
                $item = (array) $iterator->getInnerIterator();
                break;
            }
            $iterator->next();
        }

        if (array_key_exists($this->subItemsSlug, $item)) {
            $keyToRemove = array_flip([$this->subItemsSlug]);
            if ($item[$this->subItemsSlug]) {
                $item[$this->subItemsSlug] = array_map(function ($item) use($keyToRemove) {
                    // This will remove children from subset items
                    return array_diff_key($item, $keyToRemove);
                }, $item[$this->subItemsSlug]);
            }
        }

        return $item;
    }

    /**
     * Get all sub level items of node
     * @param $itemId
     * @return array|null
     * @throws \Exception
     */
    public function descendants($itemId)
    {
        $this->prepare($this->array);
        $iterator = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($this->array));
        while ($iterator->valid()) {
            if ($iterator->key() == $this->itemIdAttribute && $iterator->current() == $itemId) {
                return (array) $iterator->getInnerIterator();
            }

            $iterator->next();
        }

        return null;
    }

    /**
     * Get all parents of an item
     * @return array
     * @throws \Exception
     */
    public function parents()
    {
        return $this->prepare($this->array);
    }

    /**
     * Get first parent of an item
     * @return array
     * @throws \Exception
     */
    public function parent()
    {
        return $this->parents();
    }

    /**
     * Get all roots
     * @return array
     * @throws \Exception
     */
    public function roots(): array
    {
        $this->prepare($this->array);
        foreach ($this->array as &$item) {
            $item = array_slice($item, 0, count($item) - 1);
        }
        return $this->array;
    }

    /**
     * Get all items
     * @return array
     * @throws \Exception
     */
    public function all()
    {
        return $this->prepare($this->array);
    }
}
