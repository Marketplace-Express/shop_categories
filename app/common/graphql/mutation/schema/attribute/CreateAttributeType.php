<?php
/**
 * User: Wajdi Jurry
 * Date: ٧‏/٨‏/٢٠١٩
 * Time: ١:٠٨ ص
 */

namespace app\common\graphql\mutation\schema\attribute;


use GraphQL\Type\Definition\InputObjectType;

class CreateAttributeType extends InputObjectType
{
    public function __construct()
    {
        $config = [
            'fields' => function () {
                return [
                    'name' => self::nonNull(self::string()),
                    'values' => self::nonNull(self::listOf(self::string()))
                ];
            }
        ];
        parent::__construct($config);
    }
}
