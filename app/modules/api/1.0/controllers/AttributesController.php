<?php
/**
 * User: Wajdi Jurry
 * Date: 25/12/18
 * Time: 10:36 م
 */

namespace Shop_categories\Modules\Api\Controllers;


use Shop_categories\Controllers\ControllerBase;
use Shop_categories\RequestHandler\Attributes\{
    CreateAttributeRequestHandler,
    CreateValuesRequestHandler,
    DeleteAttributeRequestHandler,
    GetAttributeRequestHandler,
    UpdateAttributeRequestHandler
};
use Shop_categories\Services\AttributesService;

/**
 * Class AttributesController
 * @package Shop_categories\Modules\Api\Controllers
 * @RoutePrefix("/api/1.0/attributes")
 */
class AttributesController extends ControllerBase
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
            /** @var GetAttributeRequestHandler $request */
            $request = $this->getJsonMapper()->map(new \stdClass(), new GetAttributeRequestHandler());
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
            /** @var GetAttributeRequestHandler $request */
            $request = $this->getJsonMapper()->map($this->queryStringToObject($this->request->getQuery()), new GetAttributeRequestHandler());
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
     */
    public function createAction()
    {
        try {
            /** @var CreateAttributeRequestHandler $request */
            $request = $this->getJsonMapper()->map($this->request->getJsonRawBody(), new CreateAttributeRequestHandler());

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
     */
    public function updateAction($id)
    {
        try {
            /** @var UpdateAttributeRequestHandler $request */
            $request = $this->getJsonMapper()->map($this->request->getJsonRawBody(), new UpdateAttributeRequestHandler());

            if (!$request->isValid()) {
                $request->invalidRequest();
            }

            $attribute = $this->getService()->update($id, $request->toArray());
            $request->successRequest($attribute->toApiArray());
        } catch (\Throwable $exception) {
            $this->handleError($exception->getMessage(), $exception->getCode() ?: 500);
        }
    }

    /**
     * @param $id
     * @Delete('/{id:[0-9a-fA-F]{24}}')
     */
    public function deleteAction($id)
    {
        try {
            /** @var DeleteAttributeRequestHandler $request */
            $request = $this->getJsonMapper()->map(new \stdClass(), new DeleteAttributeRequestHandler());
            $request->successRequest($this->getService()->delete($id));
        } catch (\Throwable $exception) {
            $this->handleError($exception->getMessage(), $exception->getCode() ?: 500);
        }
    }

    /**
     * @param $id
     * @Post('/{id:[0-9a-fA-F]{24}}/values')
     */
    public function addValuesAction($id)
    {
        try {
            /** @var CreateValuesRequestHandler $request */
            $request = $this->getJsonMapper()->map($this->request->getJsonRawBody(), new CreateValuesRequestHandler());

            if (!$request->isValid()) {
                $request->invalidRequest();
            }

            $attribute = $this->getService()->addValues($id, $request->toArray());
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
            /** @var GetAttributeRequestHandler $request */
            $request = $this->getJsonMapper()->map(new \stdClass(), new GetAttributeRequestHandler());
            $request->successRequest($this->getService()->getValues($id));
        } catch (\Throwable $exception) {
            $this->handleError($exception->getMessage(), $exception->getCode() ?: 500);
        }
    }
}