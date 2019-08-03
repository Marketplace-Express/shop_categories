<?php
/**
 * User: Wajdi Jurry
 * Date: ٣‏/٨‏/٢٠١٩
 * Time: ٢:٠٢ ص
 */

namespace app\common\graphqlTypes;


class TypeRegistry
{
    static private $category;
    static private $attribute;

    static public function category()
    {
        return self::$category ?? self::$category = new CategoryType();
    }

    static public function attribute()
    {
        return self::$attribute ?? self::$attribute = new AttributeType();
    }
}
