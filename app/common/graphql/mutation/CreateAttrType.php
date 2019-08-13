<?php
/**
 * User: Wajdi Jurry
 * Date: ٧‏/٨‏/٢٠١٩
 * Time: ١:٠٨ ص
 */

namespace app\common\graphql\mutation;


use GraphQL\Type\Definition\InputObjectType;

class CreateAttrType extends InputObjectType
{
    public function __construct()
    {
        $config = [
            'fields' => function () {
                return [
                    'name' => self::string(),
                    'values' => self::listOf(self::string())
                ];
            }
        ];
        parent::__construct($config);
    }
}
