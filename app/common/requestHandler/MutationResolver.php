<?php
/**
 * User: Wajdi Jurry
 * Date: ١٤‏/٨‏/٢٠١٩
 * Time: ١:٢٢ م
 */

namespace app\common\requestHandler;


use app\common\exceptions\ArrayOfStringsException;
use app\common\graphql\mutation\Mutation;
use app\common\requestHandler\category\CreateRequestHandler;
use app\common\requestHandler\category\DeleteRequestHandler;
use app\common\requestHandler\category\UpdateRequestHandler;
use GraphQL\GraphQL;
use GraphQL\Type\Schema;
use Phalcon\Validation\Message\Group;

class MutationResolver extends RequestAbstract
{
    /** @var string */
    private $method;

    /** @var string */
    public $query;

    /** @var array */
    public $variables = [];

    /** @var mixed */
    private $output;

    private $requestHandlers = [
        'POST' => CreateRequestHandler::class,
        'PUT' => UpdateRequestHandler::class,
        'DELETE' => DeleteRequestHandler::class
    ];

    /**
     * MutationResolver constructor.
     */
    public function __construct()
    {
        $this->method = strtoupper($this->request->getMethod());
    }

    /**
     * @return Schema
     */
    private function getMutationSchema(): Schema
    {
        return new Schema([
            'mutation' => new Mutation()
        ]);
    }

    /**
     * @throws ArrayOfStringsException
     * @throws \Exception
     */
    public function resolve()
    {
        if (!array_key_exists('storeId', $this->variables)) {
            throw new \InvalidArgumentException('storeId is required', 400);
        }

        // Set storeId
        $this->di->getAppServices('categoryService')::setStoreId($this->variables['storeId']);

        $requestHandler = new $this->requestHandlers[$this->method];

        $output = GraphQL::executeQuery(
            $this->getMutationSchema(),
            $this->query,
            null, $requestHandler,
            $this->variables
        );

        if ($output->errors) {
            throw new ArrayOfStringsException($output->errors);
        }

        $this->output = $output;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function validate(): Group
    {
        return new Group();
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return $this->output->data;
    }
}
