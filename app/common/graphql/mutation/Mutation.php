<?php
/**
 * User: Wajdi Jurry
 * Date: ٦‏/٨‏/٢٠١٩
 * Time: ١١:٤٠ م
 */

namespace app\common\graphql\mutation;


use app\common\exceptions\ArrayOfStringsException;
use app\common\graphql\Types;
use app\common\requestHandler\IRequestHandler;
use app\common\services\CategoryService;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;

class Mutation extends ObjectType
{
    /** @var CategoryService */
    private $service;

    /** @var \JsonMapper */
    private $jsonMapper;

    /** @var string */
    private $operation;

    public function __construct()
    {
        $config = [
            'fields' => function() {
                return [
                    'create' => [
                        'args' => [
                            'storeId' => Types::uuidType(),
                            'userId' => Types::uuidType(),
                            'category' => Types::createCategorySchema()
                        ],
                        'type' => Types::mutateCategoryQuerySchema()
                    ],
                    'update' => [
                        'args' => [
                            'storeId' => self::nonNull(Types::uuidType()),
                            'userId' => self::nonNull(Types::uuidType()),
                            'category' => Types::updateCategorySchema()
                        ],
                        'type' => Types::mutateCategoryQuerySchema()
                    ],
                    'delete' => [
                        'args' => [
                            'storeId' => Types::uuidType(),
                            'category' => Types::deleteCategorySchema()
                        ],
                        'type' => self::boolean()
                    ]
                ];
            },
            'resolveField' => function ($rootValue, $args, IRequestHandler $requestHandler, ResolveInfo $info) {
                if ($this->operation) {
                    throw new ArrayOfStringsException([$info->fieldName => 'Cannot perform more than one operation at once']);
                }
                $this->operation = $info->fieldName;
                return $this->process($args, $requestHandler, $info->fieldName);
            }
        ];
        parent::__construct($config);
    }

    /**
     * @return CategoryService
     */
    private function getService()
    {
        return $this->service ?? $this->service = new CategoryService();
    }

    /**
     * @return \JsonMapper
     */
    private function getJsonMapper()
    {
        $jsonMapper = $this->jsonMapper ?? $this->jsonMapper = new \JsonMapper();
        /** To be able to pass an array to map function */
        $jsonMapper->bEnforceMapType = false;
        return $jsonMapper;
    }

    /**
     * @param array $args
     * @param IRequestHandler $requestHandler
     * @param string $operation
     * @return array
     * @throws \JsonMapper_Exception
     */
    private function process(array $args, IRequestHandler $requestHandler, string $operation = 'create')
    {
        /** @var IRequestHandler $request */
        $request = $this->getJsonMapper()->map($args, $requestHandler);
        if (!$request->isValid()) {
            $request->invalidRequest();
        }
        return $this->getService()->{$operation}($request->toArray());
    }
}
