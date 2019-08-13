<?php
/**
 * User: Wajdi Jurry
 * Date: 27/12/18
 * Time: 03:11 م
 */

namespace app\common\services;

use RedisException;
use app\common\exceptions\ArrayOfStringsException;
use app\common\repositories\AttributesRepository;
use app\common\services\cache\AttributesCache;

class AttributesService extends BaseService
{
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
        $attribute = AttributesCache::getInstance()->getAttribute($attributeId);
        if (!$attribute) {
            $attribute = AttributesRepository::getInstance()->getAttribute($attributeId)->toApiArray();
        }
        return $attribute;
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
        $attributes = AttributesCache::getInstance()->getAll($categoryId);
        if (!$attributes) {
            $attributes = AttributesRepository::getInstance()->getAll($categoryId);
        }
        return $attributes;
    }

    /**
     * Create new attribute
     *
     * @param array $attributes
     * @param string $categoryId
     * @return array
     *
     * @throws \Exception
     */
    public function create(array $attributes, string $categoryId)
    {
        // TODO: PREPARE ATTRIBUTE BEFORE CREATE
        foreach ($attributes as &$attribute) {
            $attribute = AttributesRepository::getInstance()->create($attribute, $categoryId)->toApiArray();
            try {
                AttributesCache::getInstance()->updateCache($attribute['attributeId'], $categoryId, $attribute);
            } catch (\RedisException $exception) {
                // do nothing
            }
        }
        return $attributes;
    }

    /**
     * Update existing attribute
     *
     * @param string $attributeId
     * @param array $data
     * @return array
     *
     * @throws \Exception
     */
    public function update(string $attributeId, array $data)
    {
        $attribute = AttributesRepository::getInstance()->update($attributeId, $data)->toApiArray();
        try {
            AttributesCache::getInstance()->updateCache($attributeId, $attribute['attributeCategoryId'], $attribute);
        } catch (\RedisException $exception) {
            // do nothing
        }
        return $attribute;
    }

    /**
     * Delete existing attribute
     *
     * @param string $attributeId
     *
     * @return bool
     * @throws \Exception
     */
    public function delete(string $attributeId): bool
    {
        if (AttributesRepository::getInstance()->delete($attributeId)) {
            try {
                AttributesCache::getInstance()->invalidateCache($attributeId);
            } catch (\RedisException $exception) {
                // do nothing
            }
            return true;
        }
        throw new \Exception('Attribute could not be deleted', 400);
    }

    /**
     * Add values to attribute
     *
     * @param string $attributeId
     * @param array $values
     * @return array
     *
     * @throws ArrayOfStringsException
     * @throws RedisException
     * @throws \Exception
     */
    public function updateValues(string $attributeId, array $values)
    {
        $attribute = AttributesRepository::getInstance()->updateValues($attributeId, $values)->toApiArray();
        try {
            AttributesCache::getInstance()->updateCache($attributeId, $attribute['attributeCategoryId'], $attribute);
        } catch (\RedisException $exception) {
            // do nothing
        }
        return $attribute;
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
        $attributeValues = AttributesCache::getInstance()->getValues($attributeId);
        if (!$attributeValues) {
            $attributeValues = AttributesRepository::getInstance()->getValues($attributeId);
        }
        return $attributeValues;
    }
}
