<?php
/**
 * User: Wajdi Jurry
 * Date: ٢‏/٨‏/٢٠١٩
 * Time: ٣:١٩ ص
 */

namespace app\common\graphql\query;


use app\common\graphql\TypeRegistry;
use app\common\services\CategoryService;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;

class QueryType extends ObjectType
{
    public function __construct()
    {
        $config = [
            'fields' => function () {
                return [
                    'categories' => [
                        'type' => self::listOf(TypeRegistry::category()),
                        'args' => [
                            'ids' => self::listOf(TypeRegistry::uuidType())
                        ],
                        'description' => 'List of categories'
                    ]
                ];
            },
            'resolveField' => function ($rootValue, $args, $context, ResolveInfo $info) {
                return $this->getService()->getCategories($args['ids'] ?? []);
            }
        ];
        parent::__construct($config);
    }

    /**
     * @return CategoryService
     */
    protected function getService(): CategoryService
    {
        return new CategoryService();
    }
}
