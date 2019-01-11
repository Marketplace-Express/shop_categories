<?php
/**
 * User: Wajdi Jurry
 * Date: 28/12/18
 * Time: 12:19 م
 */

namespace Shop_categories\Interfaces;


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

    public function getValues(string $attribute_id): array;

    public function addValues(string $attribute_id, array $values);
}