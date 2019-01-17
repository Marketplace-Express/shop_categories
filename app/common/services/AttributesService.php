<?php
/**
 * User: Wajdi Jurry
 * Date: 27/12/18
 * Time: 03:11 م
 */

namespace Shop_categories\Services;

use RedisException;
use Shop_categories\Exceptions\ArrayOfStringsException;

class AttributesService extends AbstractService
{
    /**
     * @return \Shop_categories\Repositories\AttributesRepository|Cache\AttributesCache
     * @throws \Exception
     */
    private function getAttributesDataSource()
    {
        try {
            return self::getAttributesCacheService();
        } catch (RedisException $exception) {
            return self::getAttributesRepository();
        } catch (\Throwable $exception) {
            throw new \Exception('No data source available for attributes');
        }
    }

    /**
     * Get attribute by id
     *
     * @param string $attributeId
     * @return array
     *
     * @throws \Exception
     */
    public function getAttribute(string $attributeId): array
    {
        return $this->getAttributesDataSource()->getAttribute($attributeId);
    }

    /**
     * Get all attributes related to a category
     *
     * @param string $categoryId
     * @return array
     *
     * @throws \Exception
     */
    public function getAll(string $categoryId)
    {
        return self::getAttributesDataSource()->getAll($categoryId);
    }

    /**
     * Create new attribute
     *
     * @param array $data
     * @return array
     *
     * @throws \Exception
     */
    public function create(array $data)
    {
        // TODO: PREPARE ATTRIBUTE BEFORE CREATE

        $attribute = self::getAttributesRepository()->create($data);
        $attribute = $attribute->toApiArray();
        self::getAttributesCacheService()->updateCache($attribute['attribute_id'], $attribute['attribute_category_id'], $attribute);
        return $attribute;
    }

    /**
     * Update existing attribute
     *
     * @param string $attributeId
     * @param array $data
     * @return \Shop_categories\Models\Attribute
     *
     * @throws RedisException
     * @throws \Exception
     */
    public function update(string $attributeId, array $data)
    {
        $attribute = self::getAttributesRepository()->update($attributeId, $data);
        self::getAttributesCacheService()->updateCache($attributeId, $attribute->attribute_category_id, $attribute->toApiArray());
        return $attribute;
    }

    /**
     * Delete existing attribute
     *
     * @param string $attributeId
     *
     * @return bool
     * @throws RedisException
     * @throws \Exception
     */
    public function delete(string $attributeId): bool
    {
        if (self::getAttributesRepository()->delete($attributeId)) {
            self::getAttributesCacheService()->invalidateCache($attributeId);
            return true;
        }
        throw new \Exception('Attribute could not be deleted', 400);
    }

    /**
     * Add values to attribute
     *
     * @param string $attributeId
     * @param array $values
     * @return bool|\Shop_categories\Models\Attribute
     *
     * @throws ArrayOfStringsException
     * @throws RedisException
     * @throws \Phalcon\Mvc\Collection\Exception
     * @throws \Exception
     */
    public function updateValues(string $attributeId, array $values)
    {
        if ($attribute = self::getAttributesRepository()->updateValues($attributeId, $values)) {
            $categoryId = self::getAttributesCacheService()->getAttribute($attributeId)['attribute_category_id'];
            self::getAttributesCacheService()->updateCache($attributeId, $categoryId, $attribute->toApiArray());
            return $attribute;
        }
        throw new ArrayOfStringsException($attribute->getMessages(), 400);
    }

    /**
     * Get attribute values
     *
     * @param string $attributeId
     * @return array
     *
     * @throws \Exception
     */
    public function getValues(string $attributeId)
    {
        return self::getAttributesDataSource()->getValues($attributeId);
    }
}