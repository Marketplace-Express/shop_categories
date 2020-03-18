<?php
/**
 * User: Wajdi Jurry
 * Date: ١٣‏/٨‏/٢٠١٩
 * Time: ١٢:٥٣ م
 */

namespace app\common\graphql\mutation\schema\category;


use app\common\graphql\Types;
use GraphQL\Type\Definition\InputObjectType;

class CreateCategoryType extends InputObjectType
{
    public function __construct()
    {
        $config = [
            'name' => 'Create',
            'fields' => function () {
                return [
                    'name' => self::nonNull(self::string()),
                    'vendorId' => Types::uuidType(),
                    'parentId' => Types::uuidType(),
                    'order' => self::int(),
                    'attributes' => self::listOf(Types::createAttributeSchema())
                ];
            }
        ];
        parent::__construct($config);
    }
}
