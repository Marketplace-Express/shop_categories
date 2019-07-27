<?php
/**
 * User: Wajdi Jurry
 * Date: 28/12/18
 * Time: 12:19 م
 */

namespace app\common\interfaces;


interface AttributeDataSourceInterface
{
    /**
     * @param string $categoryId
     * @return array|null
     */
    public function getAll(string $categoryId);

    /**
     * @param string $attribute_id
     * @return array|null
     */
    public function getAttribute(string $attribute_id);

    /**
     * @param string $attributeId
     * @return array
     */
    public function getValues(string $attributeId): array;
}