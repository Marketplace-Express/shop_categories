<?php
/**
 * User: Wajdi Jurry
 * Date: ١٣‏/٨‏/٢٠١٩
 * Time: ١:٤٦ م
 */

namespace app\common\graphql\mutation\schema\category;


use app\common\graphql\Types;
use GraphQL\Type\Definition\InputObjectType;

class DeleteCategoryType extends InputObjectType
{
    public function __construct()
    {
        $config = [
            'name' => 'Delete',
            'fields' => [
                'id' => self::nonNull(Types::uuidType())
            ]
        ];
        parent::__construct($config);
    }
}
