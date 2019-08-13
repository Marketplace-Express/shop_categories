<?php
/**
 * User: Wajdi Jurry
 * Date: ٢‏/٨‏/٢٠١٩
 * Time: ١:٤٦ ص
 */

namespace app\common\graphql\query;


use app\common\graphql\TypeRegistry;
use app\common\services\AttributesService;
use app\common\services\CategoryService;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;

class CategoryType extends ObjectType
{
    private $categoryService;
    private $attributeService;

    public function __construct()
    {
        $config = [
            'fields' => function () {
                return [
                    'id' => self::id(),
                    'name' => self::string(),
                    'children' => self::listOf(TypeRegistry::category()),
                    'parent' => self::listOf(TypeRegistry::category()),
                    'attributes' => self::listOf(TypeRegistry::attribute()),
                    'order' => self::int()
                ];
            },
            'resolveField' => function ($rootValue, $args, $context, ResolveInfo $info) {
                return $this->{'resolve'.ucfirst($info->fieldName)}($rootValue, $args, $context, $info);
            }
        ];
        parent::__construct($config);
    }

    private function getCategoryService(): CategoryService
    {
        return $this->categoryService ?? $this->categoryService = new CategoryService();
    }

    public function getAttributeService(): AttributesService
    {
        return $this->attributeService ?? $this->attributeService = new AttributesService();
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
        return $this->getCategoryService()->getParent($category['categoryId']);
    }

    public function resolveAttributes($category)
    {
        return $this->getAttributeService()->getAll($category['categoryId']);
    }

    public function resolveOrder($category)
    {
        return $category['categoryOrder'];
    }
}
