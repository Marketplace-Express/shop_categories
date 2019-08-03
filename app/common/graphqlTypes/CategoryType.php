<?php
/**
 * User: Wajdi Jurry
 * Date: ٢‏/٨‏/٢٠١٩
 * Time: ١:٤٦ ص
 */

namespace app\common\graphqlTypes;


use app\common\services\CategoryService;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;

class CategoryType extends ObjectType
{
    private $service;

    public function __construct()
    {
        $config = [
            'fields' => function () {
                return [
                    'id' => self::id(),
                    'name' => self::string(),
                    'children' => self::listOf(TypeRegistry::category()),
                    'parent' => self::listOf(TypeRegistry::category())
                ];
            },
            'resolveField' => function ($rootValue, $args, $context, ResolveInfo $info) {
                return $this->{'resolve'.ucfirst($info->fieldName)}($rootValue, $args, $context, $info);
            }
        ];
        parent::__construct($config);
    }

    private function getService(): CategoryService
    {
        return $this->service ?? $this->service = new CategoryService();
    }

    public function resolveId($category)
    {
        return $category['categoryId'];
    }

    public function resolveName($category)
    {
        return $category['categoryName'];
    }

    public function resolveChildren($category)
    {
        return !empty($category['children']) ? $category['children'] : [];
    }

    public function resolveParent($category)
    {
        return $this->getService()->getParent($category['categoryId']);
    }
}
