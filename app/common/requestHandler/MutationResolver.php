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
use app\common\validators\UuidValidator;
use GraphQL\GraphQL;
use GraphQL\Type\Schema;
use Phalcon\Validation;
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
        if (!array_key_exists($this->method, $this->requestHandlers)) {
            throw new \Exception('Unknown request method');
        }

        // Set vendorId
        $this->di->getAppServices('categoryService')::setVendorId($this->variables['vendorId']);

        $requestHandler = new $this->requestHandlers[$this->method];
        $output = GraphQL::executeQuery(
            $this->getMutationSchema(),
            $this->query,
            $requestHandler, null,
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
     * @inheritDoc
     */
    public function toArray(): array
    {
        return $this->output->toArray();
    }
}
