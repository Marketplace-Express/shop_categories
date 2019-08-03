<?php
/**
 * User: Wajdi Jurry
 * Date: ٢‏/٨‏/٢٠١٩
 * Time: ٣:١٩ ص
 */

namespace app\common\graphqlTypes;


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
                            'id' => self::string()
                        ],
                        'description' => 'List of categories'
                    ],
                ];
            },
            'resolveField' => function ($rootValue, $args, $context, ResolveInfo $info) {
                if (!empty($args['id'])) {
                    $result = [$this->getService()->getCategory($args['id'])];
                } else {
                    $result = $this->getService()->getAll();
                }
                return $result;
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
