<?php
/**
 * User: Wajdi Jurry
 * Date: 25/12/18
 * Time: 10:59 م
 */

namespace Shop_categories\Repositories;

use Shop_categories\Exceptions\ArrayOfStringsException;
use Shop_categories\Interfaces\AttributeDataSourceInterface;
use Shop_categories\Models\Attribute;

class AttributesRepository implements AttributeDataSourceInterface
{
    /**
     * @return Attribute
     */
    private static function getModel()
    {
        return Attribute::model();
    }

    /**
     * Get attribute by id
     *
     * @param string $attributeId
     * @return Attribute
     *
     * @throws \Exception
     */
    public function getAttribute(string $attributeId): Attribute
    {
        /** @var Attribute $attribute */
        $attribute = self::getModel()::findById($attributeId);
        if (!$attribute) {
            throw new \Exception('Attribute not found or maybe deleted', 404);
        }
        return $attribute;
    }

    /**
     * Get all attributes related to category
     *
     * @param string $categoryId
     * @return array
     *
     * @throws \Exception
     */
    public function getAll(string $categoryId): array
    {
        /** @var Attribute[] $attributes */
        $attributes = self::getModel()::find(['attribute_category_id' => $categoryId]);

        if (!$attributes) {
            throw new \Exception('No attributes found', 404);
        }
        $result = [];
        foreach ($attributes as $attribute) {
            $result[] = $attribute->toApiArray();
        }

        return $result;
    }

    /**
     * Create attribute
     *
     * @param array $data
     * @return Attribute
     *
     * @throws \Exception
     */
    public function create(array $data): Attribute
    {
        $attribute = self::getModel();
        $attribute->setAttributes($data);
        if (!$attribute->save()) {
            throw new ArrayOfStringsException($attribute->getMessages(), 400);
        }
        return $attribute;
    }

    /**
     * Update attribute
     *
     * @param string $attribute_id
     * @param array $data
     * @return Attribute
     *
     * @throws \Exception
     */
    public function update(string $attribute_id, array $data)
    {
        /** @var Attribute $attribute */
        $attribute = self::getModel()::findById($attribute_id);
        if (!$attribute) {
            throw new \Exception('Attribute not found or maybe deleted', 404);
        }
        $attribute->setAttributes($data);
        if (!$attribute->save()) {
            throw new ArrayOfStringsException($attribute->getMessages(), 400);
        }
        return $attribute;
    }

    /**
     * Delete attribute by id
     *
     * @param string $attribute_id
     * @return bool
     *
     * @throws \Exception
     */
    public function delete(string $attribute_id)
    {
        /** @var Attribute $attribute */
        $attribute = self::getModel()::findById($attribute_id);
        if (!$attribute) {
            throw new \Exception('Attribute not found or maybe deleted', 404);
        }
        return $attribute->delete();
    }

    /**
     * @param string $attribute_id
     * @return array
     * @throws \Exception
     */
    public function getValues(string $attribute_id): array
    {
        $attribute = self::getModel()::findById($attribute_id);
        if (!$attribute) {
            throw new \Exception('Attribute not found', 404);
        }
        return $attribute->attribute_values;
    }

    /**
     * Add new values to attribute
     *
     * @param string $attribute_id
     * @param array $values
     * @return Attribute
     *
     * @throws ArrayOfStringsException
     * @throws \Phalcon\Mvc\Collection\Exception
     * @throws \Exception
     */
    public function updateValues(string $attribute_id, array $values)
    {
        $attribute = self::getModel()::findById($attribute_id);
        if (!$attribute) {
            throw new \Exception('Attribute not found', 404);
        }
        $attribute->attribute_values = array_unique($values);
        if (!$attribute->save()) {
            throw new ArrayOfStringsException($attribute->getMessages(), 400);
        }
        return $attribute;
    }
}