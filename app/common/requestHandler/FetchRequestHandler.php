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
            'storeId',
            new UuidValidator([
                'allowEmpty' => false
            ])
        );

        return $validator->validate([
            'storeId' => $this->variables['storeId'] ?? null
        ]);
    }

    /**
     * @return array
     * @throws ArrayOfStringsException
     */
    public function toArray(): array
    {
        return $this->execute()->data['categories'];
    }

    /**
     * @return ExecutionResult
     * @throws ArrayOfStringsException
     */
    public function execute(): ExecutionResult
    {
        // Get config
        $config = \Phalcon\Di::getDefault()->getConfig()->application;

        // Limit the query depth
        DocumentValidator::addRule(new QueryDepth($config->graphql->maxQueryDepth));

        // Set storeId
        $this->di->getAppServices('categoryService')::setStoreId($this->variables['storeId']);

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
