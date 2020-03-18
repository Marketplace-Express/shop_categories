<?php
/**
 * User: Wajdi Jurry
 * Date: ١٠‏/٨‏/٢٠١٩
 * Time: ٤:٢٩ م
 */

namespace app\common\requestHandler;


use app\common\exceptions\ArrayOfStringsException;
use app\common\graphql\query\Query;
use app\common\validators\UuidValidator;
use GraphQL\Executor\ExecutionResult;
use GraphQL\GraphQL;
use GraphQL\Type\Schema;
use GraphQL\Validator\DocumentValidator;
use GraphQL\Validator\Rules\QueryDepth;
use Phalcon\Validation;
use Phalcon\Validation\Message\Group;

class FetchRequestHandler extends RequestAbstract
{
    const MAX_QUERY_DEPTH = 5;

    /** @var string */
    public $query;

    /** @var array */
    public $variables;

    /** @var mixed */
    public $rootValue;

    /**
     * @return Schema
     */
    private function getSchema(): Schema
    {
        return new Schema([
            'query' => new Query()
        ]);
    }

    /** Validate request fields using \Phalcon\Validation\Validator
     * @return Group
     */
    public function validate(): Group
    {
        $validator = new Validation();

        $validator->add(
            'vendorId',
            new Validation\Validator\Callback([
                'callback' => function ($data) {
                    if (!empty($data['vendorId'])) {
                        return new UuidValidator();
                    }
                    return false;
                },
                'message' => 'vendorId is required'
            ])
        );

        return $validator->validate([
            'vendorId' => $this->variables['vendorId'] ?? null
        ]);
    }

    /**
     * @return array
     * @throws ArrayOfStringsException
     */
    public function toArray(): array
    {
        return $this->execute()->toArray();
    }

    /**
     * @return ExecutionResult
     * @throws ArrayOfStringsException
     */
    public function execute(): ExecutionResult
    {
        // Limit the query depth
        DocumentValidator::addRule(new QueryDepth($maxDepth = self::MAX_QUERY_DEPTH));

        // Set vendorId
        $this->di->getAppServices('categoryService')::setVendorId($this->variables['vendorId']);

        $result = GraphQL::executeQuery(
            $this->getSchema(),
            $this->query,
            $this->rootValue,
            null,
            $this->variables
        );

        if (!empty($result->errors)) {
            $this->invalidRequest($result->errors);
        }

        return $result;
    }
}
