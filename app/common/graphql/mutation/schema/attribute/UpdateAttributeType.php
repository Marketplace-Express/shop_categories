<?php
/**
 * User: Wajdi Jurry
 * Date: ١٥‏/٨‏/٢٠١٩
 * Time: ٥:١٣ م
 */

namespace app\common\graphql\mutation\schema\attribute;


use app\common\graphql\Types;
use GraphQL\Type\Definition\InputObjectType;

class UpdateAttributeType extends InputObjectType
{
    public function __construct()
    {
        $config = [
            'fields' => function () {
                return [
                    'id' => Types::mongoIdType(),
                    'name' => self::string(),
                    'values' => self::listOf(self::string())
                ];
            }
        ];
        parent::__construct($config);
    }
}
