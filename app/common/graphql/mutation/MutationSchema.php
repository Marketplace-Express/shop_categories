<?php
/**
 * User: Wajdi Jurry
 * Date: ٦‏/٨‏/٢٠١٩
 * Time: ١١:٤٠ م
 */

namespace app\common\graphql\mutation;


use app\common\graphql\TypeRegistry;
use app\common\requestHandler\category\CreateRequestHandler;
use app\common\requestHandler\category\UpdateRequestHandler;
use app\common\requestHandler\IRequestHandler;
use app\common\services\CategoryService;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;

class MutationSchema extends ObjectType
{
    private $service;
    private $jsonMapper;
    private $requestHandlers = [
        'create' => CreateRequestHandler::class,
        'update' => UpdateRequestHandler::class
    ];

    public function __construct()
    {
        $config = [
            'fields' => function() {
                return [
                    'create' => [
                        'args' => [
                            'name' => self::nonNull(self::string()),
                            'parentId' => TypeRegistry::uuidType(),
                            'vendorId' => self::string(),
                            'userId' => self::string(),
                            'order' => self::int()
                        ],
                        'type' => TypeRegistry::category(),
                    ],
                    'update' => [
                        'args' => [
                            'id' => self::nonNull(TypeRegistry::uuidType()),
                            'name' => self::string(),
                            'parentId' => TypeRegistry::uuidType()
                        ],
                        'type' => TypeRegistry::category()
                    ]
                ];
            },
            'resolveField' => function ($controller, $args, $context, ResolveInfo $info) {
                return $this->save($args, new $this->requestHandlers[$info->fieldName]($controller), $info->fieldName);
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
        return $this->jsonMapper ?? $this->jsonMapper = new \JsonMapper();
    }

    /**
     * @param array $data
     * @return mixed
     */
    private function arrayToObject(array $data)
    {
        return json_decode(json_encode($data)) ?? new \stdClass();
    }

    /**
     * @param array $args
     * @param IRequestHandler $requestHandler
     * @param string $operation
     * @return array
     * @throws \JsonMapper_Exception
     * @throws \Phalcon\Exception
     * @throws \app\common\exceptions\ArrayOfStringsException
     */
    private function save(array $args, IRequestHandler $requestHandler, string $operation = 'create')
    {
        /** @var CreateRequestHandler|UpdateRequestHandler $request */
        $request = $this->getJsonMapper()->map($this->arrayToObject($args), $requestHandler);
        if (!$request->isValid()) {
            $request->invalidRequest();
        }
        $category = $this->getService()->{$operation}($request->toArray());
        return $category;
    }
}
