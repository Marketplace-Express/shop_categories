<?php
/**
 * User: Wajdi Jurry
 * Date: 25/12/18
 * Time: 10:59 م
 */

namespace app\common\repositories;

use app\common\exceptions\NotFoundException;
use app\common\exceptions\ArrayOfStringsException;
use app\common\interfaces\AttributeDataSourceInterface;
use app\common\models\Attribute;
use MongoDB\BSON\ObjectId;

class AttributesRepository implements AttributeDataSourceInterface
{
    /**
     * @return AttributesRepository
     */
    static public function getInstance()
    {
        return new self;
    }

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
            throw new NotFoundException('Attribute not found or maybe deleted');
        }
        return $attribute;
    }

    /**
     * Get all attributes related to category
     *
     * @param string $categoryId
     * @return array
     *
     */
    public function getAll(string $categoryId): array
    {
        $attributes = self::getModel()::find([
            'conditions' => [
                'attribute_category_id' => $categoryId
            ]
        ]);

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
     * @param string $categoryId
     * @return Attribute
     *
     * @throws ArrayOfStringsException
     * @throws \Phalcon\Mvc\Collection\Exception
     * @throws \Exception
     */
    public function create(array $data, string $categoryId): Attribute
    {
        $attribute = self::getModel();

        $data['attribute_category_id'] = $categoryId;
        $attribute->setAttributes($data);

        if (!$attribute->save()) {
            throw new ArrayOfStringsException($attribute->getMessages(), 400);
        }
        return $attribute;
    }

    /**
     * Update attribute
     *
     * @param string $categoryId
     * @param array $data
     * @return Attribute
     *
     * @throws \Exception
     */
    public function update(string $categoryId, array $data)
    {
        /** @var Attribute $attribute */
        $attribute = self::getModel()::findFirst([
            [
                'attribute_category_id' => $categoryId,
                '_id' => new ObjectId($data['attribute_id'])
            ]
        ]);
        if (!$attribute) {
            throw new NotFoundException('Attribute not found or maybe deleted');
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
            throw new NotFoundException('Attribute not found or maybe deleted');
        }
        return $attribute->delete();
    }

    /**
     * @param string $attributeId
     * @return array
     * @throws \Exception
     */
    public function getValues(string $attributeId): array
    {
        $attribute = self::getModel()::findById($attributeId);
        if (!$attribute) {
            throw new NotFoundException('Attribute not found');
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
            throw new NotFoundException('Attribute not found');
        }
        $attribute->attribute_values = array_unique($values);
        if (!$attribute->save()) {
            throw new ArrayOfStringsException($attribute->getMessages(), 400);
        }
        return $attribute;
    }
}
