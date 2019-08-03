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

    static public function category()
    {
        return self::$category ?? self::$category = new CategoryType();
    }
}
