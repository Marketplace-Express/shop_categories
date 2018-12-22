<?php
/**
 * User: Wajdi Jurry
 * Date: 14/09/18
 * Time: 03:01 ص
 */

namespace Shop_categories\Helpers;

class ArrayHelper
{
    private $parentIdAttribute;
    private $itemIdAttribute;
    private $subItemsSlug;
    private $noParentValue;

    private $array;

    public function __construct(array $array, array $params)
    {
        $this->array = $array;

        $this->itemIdAttribute = $params['itemIdAttribute'];
        $this->parentIdAttribute = $params['parentIdAttribute'];
        $this->subItemsSlug = $params['subItemsSlug'] ?: 'children';
        $this->noParentValue = $params['noParentValue'] ?: null;
    }

    /**
     * Creating multidimensional array from one-dimensional array, based on itemId and parentId
     * @return array
     * @throws \Exception
     */
    public function tree()
    {
        $ids = array_column($this->array, $this->itemIdAttribute);
        foreach ($this->array as $key => &$value) {
            if ($value[$this->parentIdAttribute] != $this->noParentValue) {
                $iterator = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($this->array), \RecursiveIteratorIterator::CHILD_FIRST);
                while ($iterator->valid()) {
                    if ($iterator->key() == $this->itemIdAttribute && $iterator->current() == $value[$this->parentIdAttribute]) {
                        if (array_key_exists($this->subItemsSlug, (array) $iterator->getInnerIterator())) {
                            $iterator->offsetSet($this->subItemsSlug, array_merge((array)$iterator->offsetGet($this->subItemsSlug), [$value]));
                        } else {
                            $iterator->offsetSet($this->subItemsSlug, [$value]);
                        }
                        $current = (array) $iterator->getSubIterator($iterator->getDepth());
                        for ($i = $iterator->getDepth()-1; $i > 0; $i--) {
                            $parents = (array) $iterator->getSubIterator($i);
                            if (array_key_exists($this->subItemsSlug, $parents)) {
                                $parents[$this->subItemsSlug] = $current;
                            } else {
                                $parentItemsIndexes = array_column($parents, $this->itemIdAttribute);
                                $parentIndex = array_search($current[$this->itemIdAttribute], $parentItemsIndexes);
                                $parents[$parentIndex] = $current;
                            }
                            $current = $parents;
                        }
                        $this->array[array_search($current[$this->itemIdAttribute], $ids)] = $current;
                        unset($this->array[$key]);
                        break;
                    }
                    $iterator->next();
                }
            }
        }
        return array_values($this->array);
    }
}
