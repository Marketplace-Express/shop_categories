<?php
/**
 * User: Wajdi Jurry
 * Date: ٣‏/٨‏/٢٠١٩
 * Time: ٢:٠٢ ص
 */

namespace app\common\graphql;


use app\common\graphql\mutation\schema\category\CreateCategoryType;
use app\common\graphql\mutation\schema\category\DeleteCategoryType;
use app\common\graphql\mutation\schema\category\UpdateCategoryType;
use app\common\graphql\mutation\schema\attribute\CreateAttributeType;
use app\common\graphql\mutation\schema\attribute\UpdateAttributeType;
use app\common\graphql\query\schema\Attribute;
use app\common\graphql\query\schema\Category;
use app\common\graphql\query\schema\MutationResultQuery;
use app\common\graphql\scalarTypes\MongoIdType;
use app\common\graphql\scalarTypes\UuidType;

class Types
{
    /** @var Category */
    static private $queryCategorySchema;

    /** @var Attribute */
    static private $queryAttributeSchema;

    /** @var  */
    static private $createCategorySchema;

    /** @var MutationResultQuery */
    static private $mutateCategoryQuerySchema;

    /** @var UpdateCategoryType */
    static private $updateCategorySchema;

    /** @var DeleteCategoryType */
    static private $deleteCategorySchema;

    /** @var CreateAttributeType */
    static private $createAttributeSchema;

    /** @var UpdateAttributeType */
    static private $updateAttributeSchema;

    /** @var UuidType */
    static private $uuid;

    /** @var MongoIdType */
    static private $mongoId;

    static public function queryCategorySchema()
    {
        return self::$queryCategorySchema ?? self::$queryCategorySchema = new Category();
    }

    static public function queryAttributeSchema()
    {
        return self::$queryAttributeSchema ?? self::$queryAttributeSchema = new Attribute();
    }

    static public function mutateCategoryQuerySchema()
    {
        return self::$mutateCategoryQuerySchema ?? self::$mutateCategoryQuerySchema = new MutationResultQuery();
    }

    static public function createCategorySchema()
    {
        return self::$createCategorySchema ?? self::$createCategorySchema = new CreateCategoryType();
    }

    static public function updateCategorySchema()
    {
        return self::$updateCategorySchema ?? self::$updateCategorySchema = new UpdateCategoryType();
    }

    static public function deleteCategorySchema()
    {
        return self::$deleteCategorySchema ?? self::$deleteCategorySchema = new DeleteCategoryType();
    }

    static public function createAttributeSchema()
    {
        return self::$createAttributeSchema ?? self::$createAttributeSchema = new CreateAttributeType();
    }

    static public function updateAttributeSchema()
    {
        return self::$updateAttributeSchema ?? self::$updateAttributeSchema = new UpdateAttributeType();
    }

    static public function uuidType()
    {
        return self::$uuid ?? self::$uuid = new UuidType();
    }

    static public function mongoIdType()
    {
        return self::$mongoId ?? self::$mongoId = new MongoIdType();
    }
}
