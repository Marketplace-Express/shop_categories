<?php
/**
 * User: Wajdi Jurry
 * Date: 25/12/18
 * Time: 10:36 م
 */

namespace Shop_categories\Modules\Api\Controllers;


use Shop_categories\Controllers\BaseController;
use Shop_categories\RequestHandler\Attribute\{
    CreateRequestHandler,
    UpdateValuesRequestHandler,
    DeleteRequestHandler,
    GetRequestHandler,
    UpdateRequestHandler
};
use Shop_categories\Services\AttributesService;

/**
 * Class AttributesController
 * @package Shop_categories\Modules\Api\Controllers
 * @RoutePrefix("/api/1.0/attributes")
 */
class AttributesController extends BaseController
{
    public function initialize()
    {
        $this->setService(new AttributesService());
    }

    /**
     * @return AttributesService
     */
    private function getService(): AttributesService
    {
        return $this->service;
    }

    /**
     * @param $id
     * @Get('/{id:[0-9a-fA-F]{24}}')
     */
    public function getAction($id)
    {
        try {
            /** @var GetRequestHandler $request */
            $request = $this->getJsonMapper()->map(new \stdClass(), new GetRequestHandler());
            $request->successRequest($this->getService()->getAttribute($id));
        } catch (\Throwable $exception) {
            $this->handleError($exception->getMessage(), $exception->getCode() ?: 500);
        }
    }

    /**
     * @Get('')
     */
    public function getAllAction()
    {
        try {
            /** @var GetRequestHandler $request */
            $request = $this->getJsonMapper()->map($this->queryStringToObject($this->request->getQuery()), new GetRequestHandler());
            if (!$request->isValid()) {
                $request->invalidRequest();
            }
            $request->successRequest($this->getService()->getAll($request->getCategoryId()));
        } catch (\Throwable $exception) {
            $this->handleError($exception->getMessage(), $exception->getCode() ?: 500);
        }
    }

    /**
     * @Post('')
     * @AuthMiddleware("\Shop_categories\Events\Middleware\RequestMiddlewareEvent")
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
     * @param $id
     * @Put('/{id:[0-9a-fA-F]{24}}')
     * @AuthMiddleware("\Shop_categories\Events\Middleware\RequestMiddlewareEvent")
     */
    public function updateAction($id)
    {
        try {
            /** @var UpdateRequestHandler $request */
            $request = $this->getJsonMapper()->map($this->request->getJsonRawBody(), new UpdateRequestHandler());

            if (!$request->isValid()) {
                $request->invalidRequest();
            }

            $attribute = $this->getService()->update($id, $request->toArray());
            $request->successRequest($attribute);
        } catch (\Throwable $exception) {
            $this->handleError($exception->getMessage(), $exception->getCode() ?: 500);
        }
    }

    /**
     * @param $id
     * @Delete('/{id:[0-9a-fA-F]{24}}')
     * @AuthMiddleware("\Shop_categories\Events\Middleware\RequestMiddlewareEvent")
     */
    public function deleteAction($id)
    {
        try {
            /** @var DeleteRequestHandler $request */
            $request = $this->getJsonMapper()->map(new \stdClass(), new DeleteRequestHandler());
            $request->successRequest($this->getService()->delete($id));
        } catch (\Throwable $exception) {
            $this->handleError($exception->getMessage(), $exception->getCode() ?: 500);
        }
    }

    /**
     * @param $id
     * @Post('/{id:[0-9a-fA-F]{24}}/values')
     * @AuthMiddleware("\Shop_categories\Events\Middleware\RequestMiddlewareEvent")
     */
    public function updateValuesAction($id)
    {
        try {
            /** @var UpdateValuesRequestHandler $request */
            $request = $this->getJsonMapper()->map($this->request->getJsonRawBody(), new UpdateValuesRequestHandler());

            if (!$request->isValid()) {
                $request->invalidRequest();
            }

            $attribute = $this->getService()->updateValues($id, $request->toArray());
            $request->successRequest($attribute->toApiArray());
        } catch (\Throwable $exception) {
            $this->handleError($exception->getMessage(), $exception->getCode() ?: 500);
        }
    }

    /**
     * @param $id
     * @Get('/{id:[0-9a-fA-F]{24}}/values')
     */
    public function getValuesAction($id)
    {
        try {
            /** @var GetRequestHandler $request */
            $request = $this->getJsonMapper()->map(new \stdClass(), new GetRequestHandler());
            $request->successRequest($this->getService()->getValues($id));
        } catch (\Throwable $exception) {
            $this->handleError($exception->getMessage(), $exception->getCode() ?: 500);
        }
    }
}