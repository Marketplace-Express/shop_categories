<?php
/**
 * User: Wajdi Jurry
 * Date: ١٤‏/٨‏/٢٠١٩
 * Time: ١٢:١٠ م
 */

namespace app\common\graphql\query\schema;


use app\common\graphql\Types;
use app\common\services\AttributesService;
use app\common\services\CategoryService;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;

class MutationResultQuery extends ObjectType
{
    /** @var CategoryService */
    private $categoryService;

    /** @var AttributesService */
    private $attributesService;

    public function __construct()
    {
        $config = [
            'fields' => function () {
                return [
                    'id' => self::id(),
                    'name' => self::string(),
                    'url' => self::string(),
                    'parent' => Types::mutateCategoryQuerySchema(),
                    'vendorId' => self::string(),
                    'userId' => self::string(),
                    'order' => self::int(),
                    'attributes' => self::listOf(Types::queryAttributeSchema())
                ];
            },
            'resolveField' => function ($rootValue, $args, $context, ResolveInfo $info) {
                return $this->{'resolve' . ucfirst($info->fieldName)}($rootValue);
            }
        ];
        parent::__construct($config);
    }

    /**
     * @return CategoryService
     */
    protected function getCategoryService()
    {
        return $this->categoryService ?? $this->categoryService = new CategoryService();
    }

    /**
     * @return AttributesService
     */
    protected function getAttributesService()
    {
        return $this->attributesService ?? $this->attributesService = new AttributesService();
    }

    private function resolveId($category)
    {
        return $category['id'];
    }

    private function resolveName($category)
    {
        return $category['name'];
    }

    private function resolveUrl($category)
    {
        return $category['url'];
    }

    private function resolveParent($category)
    {
        $parent = $this->getCategoryService()->getParent($category['id']);
        return array_shift($parent);
    }

    private function resolveVendorId($category)
    {
        return $category['vendorId'];
    }

    private function resolveUserId($category)
    {
        return $category['userId'];
    }

    private function resolveOrder($category)
    {
        return $category['order'];
    }

    private function resolveAttributes($category)
    {
        return $this->getAttributesService()->getAll($category['id']);
    }
}
