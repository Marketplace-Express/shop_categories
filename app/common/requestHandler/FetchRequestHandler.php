<?php
/**
 * User: Wajdi Jurry
 * Date: ١٠‏/٨‏/٢٠١٩
 * Time: ٤:٢٩ م
 */

namespace app\common\requestHandler;


use app\common\exceptions\ArrayOfStringsException;
use app\common\graphql\query\Query;
use GraphQL\Executor\ExecutionResult;
use GraphQL\GraphQL;
use GraphQL\Type\Schema;
use GraphQL\Validator\DocumentValidator;
use GraphQL\Validator\Rules\QueryDepth;
use Phalcon\Mvc\Controller;
use Phalcon\Validation\Message\Group;

class FetchRequestHandler extends RequestAbstract
{
    const MAX_QUERY_DEPTH = 5;

    /** @var string */
    private $query;

    /** @var array */
    private $variables;

    /** @var mixed */
    private $rootValue;

    /**
     * FetchRequestHandler constructor.
     * @param Controller $controller
     * @param mixed $rootValue
     */
    public function __construct(Controller $controller, $rootValue = null)
    {
        $this->rootValue = $rootValue;
        parent::__construct($controller);
    }

    /**
     * @param string $query
     */
    public function setQuery(string $query)
    {
        $this->query = $query;
    }

    /**
     * @param array|null $variables
     */
    public function setVariables(?array $variables)
    {
        $this->variables = $variables;
    }

    /**
     * @return string
     */
    protected function getQuery(): string
    {
        return $this->query;
    }

    /**
     * @return array|null
     */
    protected function getVariables(): ?array
    {
        return $this->variables;
    }

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
        return new Group();
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

        $result = GraphQL::executeQuery(
            $this->getSchema(),
            $this->getQuery(),
            $this->rootValue,
            null,
            $this->getVariables()
        );

        if (!empty($result->errors)) {
            $this->invalidRequest($result->errors);
        }

        return $result;
    }
}
