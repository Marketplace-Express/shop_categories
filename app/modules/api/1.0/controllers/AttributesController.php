<?php
/**
 * User: Wajdi Jurry
 * Date: 25/12/18
 * Time: 10:36 م
 */

namespace app\modules\api\controllers;


use app\common\controllers\BaseController;
use app\common\requestHandler\attribute\{
    CreateRequestHandler,
    UpdateValuesRequestHandler,
    DeleteRequestHandler,
    GetRequestHandler,
    UpdateRequestHandler
};
use app\common\services\AttributesService;

/**
 * Class AttributesController
 * @package app\modules\api\controllers
 * @RoutePrefix("/api/1.0/attributes")
 */
class AttributesController extends BaseController
{
    public function initialize()
    {
        $vendorId = $this->request->getQuery('vendorId');
        if (!$vendorId) {
            $this->sendResponse('Please provide a valid vendor Id', 400);
        }
        $service = new AttributesService();
        $service::setVendorId($vendorId);
        $this->setService($service);
    }

    /**
     * @return AttributesService
     */
    private function getService(): AttributesService
    {
        return new AttributesService();
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
     * @param $id
     * @Put('/{id:[0-9a-fA-F]{24}}')
     * @AuthMiddleware("\app\common\events\middleware\RequestMiddlewareEvent")
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
     * @AuthMiddleware("\app\common\events\middleware\RequestMiddlewareEvent")
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
     * @AuthMiddleware("\app\common\events\middleware\RequestMiddlewareEvent")
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