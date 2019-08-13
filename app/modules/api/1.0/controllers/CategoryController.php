<?php
namespace app\modules\api\controllers;

use app\common\controllers\BaseController;
use app\common\exceptions\ArrayOfStringsException;
use app\common\graphql\mutation\MutationSchema;
use app\common\graphql\query\QueryType;
use app\common\requestHandler\FetchRequestHandler;
use app\common\requestHandler\category\{
    CreateRequestHandler,
    DeleteRequestHandler,
    UpdateRequestHandler
};
use app\common\services\CategoryService;
use GraphQL\GraphQL;
use GraphQL\Type\Schema;

/**
 * Class CategoryController
 * @package app\modules\api\controllers
 * @RoutePrefix("/api/1.0/categories")
 */
class CategoryController extends BaseController
{
    public function initialize()
    {
        if (empty($vendorId = $this->request->getQuery('vendorId')) || !$this->uuidUtil->isValid($vendorId)) {
            $this->sendResponse('Please provide a valid vendor Id', 400);
        }
        $service = new CategoryService();
        $service::setVendorId($vendorId);
        $this->setService($service);
    }

    /**
     * @return CategoryService
     */
    private function getService(): CategoryService
    {
        return $this->service;
    }

    /**
     * @return Schema
     */
    protected function getQuerySchema(): Schema
    {
        return new Schema([
            'query' => new QueryType()
        ]);
    }

    /**
     * @return Schema
     */
    protected function getMutationSchema(): Schema
    {
        return new Schema([
            'mutation' => new MutationSchema()
        ]);
    }

    /**
     * @Post('/fetch')
     */
    public function fetchAction()
    {
        try {
            // NOTE: THIS RULES SHOULD BE DYNAMIC PER USER (PER ROLE)
            /** @var FetchRequestHandler $request */
            $request = $this->getJsonMapper()->map($this->request->getJsonRawBody(), new FetchRequestHandler($this));
            if (!$request->isValid()) {
                $request->invalidRequest();
            }
            $request->successRequest($request->toArray());
        } catch (\Throwable $exception) {
            $this->handleError($exception->getMessage(), $exception->getCode());
        }
    }

    /**
     * @Post('/save')
     */
    public function saveAction()
    {
        try {
            $output = GraphQL::executeQuery(
                $this->getMutationSchema(),
                $this->request->getJsonRawBody(true)['query'],
                $this, null,
                $this->request->getJsonRawBody(true)['variables']
            );
            if ($output->errors) {
                throw new ArrayOfStringsException($output->errors);
            }
            $this->sendResponse($output->toArray(), 200);
        } catch (\Throwable $exception) {
            $this->handleError($exception->getMessage(), $exception->getCode());
        }
    }

    /**
     * Delete category
     * @Delete('/{id:[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}}')
     * @param $categoryId
     * @AuthMiddleware("\app\common\events\middleware\RequestMiddlewareEvent")
     */
    public function deleteAction($categoryId)
    {
        try {
            /** @var DeleteRequestHandler $request */
            $request = $this->getJsonMapper()->map(new \stdClass(), new DeleteRequestHandler());
            $this->getService()->delete($categoryId);
            $request->successRequest('Deleted');
        } catch (\Throwable $exception) {
            $this->handleError($exception->getMessage(), $exception->getCode() ?: 500);
        }
    }
}
