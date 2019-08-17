<?php
/**
 * User: Wajdi Jurry
 * Date: 14/09/18
 * Time: 03:01 ص
 */

namespace app\common\helpers;

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
        $this->subItemsSlug = 'children';
        $this->noParentValue = null;
        if (array_key_exists('subItemsSlug', $params)) {
            $this->subItemsSlug = $params['subItemsSlug'];
        }
        if (array_key_exists('noParentValue', $params)) {
            $this->noParentValue = $params['noParentValue'];
        }
    }

//    public function tree() {
//        $hierarchy = array(); // -- Stores the final data
//        $itemReferences = array(); // -- temporary array, storing references to all items in a single-dimention
//        foreach ( $this->array as $item ) {
//            $id       = $item[$this->itemIdAttribute];
//            $parentId = $item[$this->parentIdAttribute];
//            if (isset($itemReferences[$parentId])) { // parent exists
//                $itemReferences[$parentId][$this->subItemsSlug][$id] = $item; // assign item to parent
//                $itemReferences[$id] =& $itemReferences[$parentId][$this->subItemsSlug][$id]; // reference parent's item in single-dimentional array
//            } elseif (!$parentId || !isset($hierarchy[$parentId])) { // -- parent Id empty or does not exist. Add it to the root
//                $hierarchy[$id] = $item;
//                $itemReferences[$id] =& $hierarchy[$id];
//            }
//        }
//        unset($this->array, $item, $id, $parentId);
//        // -- Run through the root one more time. If any child got added before it's parent, fix it.
//        foreach ( $hierarchy as $id => &$item ) {
//            $parentId = $item[$this->parentIdAttribute];
//            if ( isset($itemReferences[$parentId] ) ) { // -- parent DOES exist
//                $itemReferences[$parentId][$this->subItemsSlug][$id] = $item; // -- assign it to the parent's list of children
//                unset($hierarchy[$id]); // -- remove it from the root of the hierarchy
//            }
//        }
//        unset($itemReferences, $id, $item, $parentId);
//        return $hierarchy;
//    }

    /**
     * Creating multidimensional array from one-dimensional array, based on itemId and parentId
     * @return array
     * @throws \Exception
     */
    public function tree()
    {
        // TODO ENHANCE PERFORMANCE
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
