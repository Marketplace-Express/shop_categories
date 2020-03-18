<?php
/**
 * User: Wajdi Jurry
 * Date: ١٣‏/٨‏/٢٠١٩
 * Time: ١٢:٥٩ م
 */

namespace app\common\graphql\mutation\schema\category;


use app\common\graphql\Types;
use GraphQL\Type\Definition\InputObjectType;

class UpdateCategoryType extends InputObjectType
{
    public function __construct()
    {
        $config = [
            'name' => 'Update',
            'fields' => function () {
                return [
                    'id' => self::nonNull(Types::uuidType()),
                    'vendorId' => Types::uuidType(),
                    'name' => self::string(),
                    'parentId' => Types::uuidType(),
                    'attributes' => [
                        'type' => self::listOf(Types::updateAttributeSchema())
                    ]
                ];
            }
        ];
        parent::__construct($config);
    }
}
