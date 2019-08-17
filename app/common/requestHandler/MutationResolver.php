<?php
/**
 * User: Wajdi Jurry
 * Date: ١٤‏/٨‏/٢٠١٩
 * Time: ١:٢٢ م
 */

namespace app\common\requestHandler;


use app\common\controllers\BaseController;
use app\common\exceptions\ArrayOfStringsException;
use app\common\graphql\mutation\Mutation;
use app\common\requestHandler\category\CreateRequestHandler;
use app\common\requestHandler\category\DeleteRequestHandler;
use app\common\requestHandler\category\UpdateRequestHandler;
use GraphQL\GraphQL;
use GraphQL\Type\Schema;
use Phalcon\Mvc\Controller;

class MutationResolver
{
    /** @var BaseController */
    private $controller;

    /** @var string */
    private $method;

    /** @var string */
    private $query;

    /** @var array */
    private $variables = [];

    private $requestHandlers = [
        'POST' => CreateRequestHandler::class,
        'PUT' => UpdateRequestHandler::class,
        'DELETE' => DeleteRequestHandler::class
    ];

    public function __construct(Controller $controller)
    {
        $this->controller = $controller;
        $this->method = strtoupper($controller->request->getMethod());
        $this->query = $controller->request->getJsonRawBody(true)['query'];
        $this->variables = $controller->request->getJsonRawBody(true)['variables'];
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
     * @return array
     * @throws ArrayOfStringsException
     * @throws \Exception
     */
    public function resolve()
    {
        if (!array_key_exists($this->method, $this->requestHandlers)) {
            throw new \Exception('Unknown request method');
        }

        if (!$this->controller) {
            throw new \Exception('Controller is not provided', 500);
        }

        $requestHandler = new $this->requestHandlers[$this->method]($this->controller);
        $output = GraphQL::executeQuery(
            $this->getMutationSchema(),
            $this->query,
            $requestHandler, null,
            $this->variables
        );
        if ($output->errors) {
            throw new ArrayOfStringsException($output->errors);
        }
        return $output->toArray();
    }
}
