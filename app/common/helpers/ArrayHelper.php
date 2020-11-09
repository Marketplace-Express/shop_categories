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


    /**
     * @return array
     */
    public function tree()
    {
        // First, convert the array so that the keys match the ids
        $reKeyed = array();
        foreach ($this->array as $item) {
            $reKeyed[$item[$this->itemIdAttribute]] = $item;
        }

        // Next, use references to associate children with parents
        foreach ($reKeyed as $id => $item) {
            if (isset($item[$this->parentIdAttribute], $reKeyed[$item[$this->parentIdAttribute]])) {
                $reKeyed[$item[$this->parentIdAttribute]][$this->subItemsSlug][] =& $reKeyed[$id];
            }
        }

        // Finally, go through and remove children from the outer level
        foreach ($reKeyed as $id => $item) {
            if (isset($item[$this->parentIdAttribute])) {
                unset($reKeyed[$id]);
            }
        }

        return $reKeyed;
    }
}
