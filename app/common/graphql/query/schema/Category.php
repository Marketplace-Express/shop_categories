<?php
/**
 * User: Wajdi Jurry
 * Date: ٢‏/٨‏/٢٠١٩
 * Time: ١:٤٦ ص
 */

namespace app\common\graphql\query\schema;


use app\common\graphql\Types;
use app\common\services\AttributesService;
use app\common\services\CategoryService;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;

class Category extends ObjectType
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
                    'url' => self::string(),
                    'children' => self::listOf(Types::queryCategorySchema()),
                    'parent' => self::listOf(Types::queryCategorySchema()),
                    'attributes' => self::listOf(Types::queryAttributeSchema()),
                    'order' => self::int()
                ];
            },
            'resolveField' => function ($rootValue, $args, $context, ResolveInfo $info) {
                return $this->{'resolve'.ucfirst($info->fieldName)}($rootValue, $args, $context, $info);
            }
        ];
        parent::__construct($config);
    }

    protected function getCategoryService(): CategoryService
    {
        return $this->categoryService ?? $this->categoryService = new CategoryService();
    }

    protected function getAttributeService(): AttributesService
    {
        return $this->attributeService ?? $this->attributeService = new AttributesService();
    }

    private function resolveId($category)
    {
        return $category['id'];
    }

    private function resolveName($category)
    {
        return $category['name'];
    }

    private function resolveChildren($category)
    {
        return !empty($category['children']) ? $category['children'] : [];
    }

    private function resolveParent($category)
    {
        return $this->getCategoryService()->getParent($category['id']);
    }

    private function resolveAttributes($category)
    {
        return $this->getAttributeService()->getAll($category['id']);
    }

    private function resolveOrder($category)
    {
        return $category['order'];
    }

    private function resolveUrl($category)
    {
        return $category['url'];
    }
}
