<?php
/**
 * User: Wajdi Jurry
 * Date: ٣‏/٨‏/٢٠١٩
 * Time: ٢:٠٢ ص
 */

namespace app\common\graphql;


use app\common\graphql\mutation\CreateAttrType;
use app\common\graphql\mutation\MutationSchema;
use app\common\graphql\query\AttributeType;
use app\common\graphql\query\CategoryType;
use app\common\graphql\scalarTypes\UuidType;

class TypeRegistry
{
    /** @var CategoryType */
    static private $category;

    /** @var AttributeType */
    static private $attribute;

    /** @var MutationSchema */
    static private $create;

    /** @var CreateAttrType */
    static private $createAttr;

    /** @var UuidType */
    static private $uuid;

    static public function category()
    {
        return self::$category ?? self::$category = new CategoryType();
    }

    static public function attribute()
    {
        return self::$attribute ?? self::$attribute = new AttributeType();
    }

    static public function create()
    {
        return self::$create ?? self::$create = new MutationSchema();
    }

    static public function createAttr()
    {
        return self::$createAttr ?? self::$createAttr = new CreateAttrType();
    }

    static public function uuidType()
    {
        return self::$uuid ?? self::$uuid = new UuidType();
    }
}
