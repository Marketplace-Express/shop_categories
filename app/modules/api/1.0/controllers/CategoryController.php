<?php
namespace app\modules\api\controllers;

use app\common\controllers\BaseController;
use app\common\graphqlTypes\QueryType;
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
     * @return Schema
     */
    protected function getSchema(): Schema
    {
        return new Schema([
            'query' => new QueryType()
        ]);
    }

    /**
     * @return CategoryService
     */
    private function getService(): CategoryService
    {
        return $this->service;
    }

    /**
     * Create category
     * Send response on success/fail
     * @Post('')
     * @AuthMiddleware("\app\common\events\middleware\RequestMiddlewareEvent")
     */
    public function createAction()
    {
        try {
            /** @var CreateRequestHandler $request */
            $request = $this->getJsonMapper()->map($this->request->getJsonRawBody(), new CreateRequestHandler());

            if (!$request->isValid()) {
                $request->invalidRequest();
            }

            $request->successRequest($this->getService()->create($request->toArray()));
        } catch (\Throwable $exception) {
            $this->handleError($exception->getMessage(), $exception->getCode() ?: 500);
        }
    }

    /**
     * Update category
     * @Put('/{id:[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}}')
     * @param $categoryId
     * @AuthMiddleware("\app\common\events\middleware\RequestMiddlewareEvent")
     */
    public function updateAction($categoryId)
    {
        try {
            /** @var UpdateRequestHandler $request */
            $request = $this->getJsonMapper()->map($this->request->getJsonRawBody(), new UpdateRequestHandler());

            if (!$request->isValid()) {
                $request->invalidRequest();
            }

            $request->successRequest($this->getService()->update($categoryId, $request->toArray()));
        } catch (\Throwable $exception) {
            $this->handleError($exception->getMessage(), $exception->getCode() ?: 500);
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

    /**
     * @Post('/fetch')
     */
    public function fetchAction()
    {
        try {
            // TODO: ADD QUERY VALIDATION RULES
            // NOTE: THIS RULES SHOULD BE DYNAMIC PER USER (PER ROLE)
            $output = GraphQL::executeQuery(
                $this->getSchema(),
                $this->request->getJsonRawBody(true)['query']
            );
            if ($output->errors) {
                if ($output->errors[0]->getPrevious() instanceof \Throwable) {
                    throw $output->errors[0]->getPrevious();
                }
                throw new \Exception($output->errors[0]->getMessage(), 500);
            }
            $this->sendResponse($output->toArray()['data'], 200);
        } catch (\Throwable $exception) {
            $this->handleError($exception->getMessage(), $exception->getCode());
        }
    }
}
