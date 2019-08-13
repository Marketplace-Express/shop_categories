<?php
/**
 * User: Wajdi Jurry
 * Date: ٣‏/٨‏/٢٠١٩
 * Time: ٦:١٩ م
 */

namespace app\common\graphql\query;


use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;

class AttributeType extends ObjectType
{
    public function __construct()
    {
        $config = [
            'fields' => [
                'id' => self::id(),
                'name' => self::string(),
                'key' => self::string(),
                'values' => self::listOf(self::string())
            ],
            'resolveField' => function ($rootValue, $args, $context, ResolveInfo $info) {
                return $this->{'resolve'.ucfirst($info->fieldName)}($rootValue, $args);
            }
        ];
        parent::__construct($config);
    }

    public function resolveId($attribute)
    {
        return $attribute['attributeId'];
    }

    public function resolveName($attribute)
    {
        return $attribute['attributeName'];
    }

    public function resolveKey($attribute)
    {
        return $attribute['attributeKey'];
    }

    public function resolveValues($attribute)
    {
        return $attribute['attributeValues'];
    }
}
