<?php
namespace app\modules\api\controllers;

use app\common\controllers\BaseController;
use app\common\requestHandler\FetchRequestHandler;
use app\common\requestHandler\MutationResolver;
use app\common\services\CategoryService;

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
    }

    /**
     * @Post('/fetch')
     */
    public function fetchAction()
    {
        try {
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
     * @Post('/mutate')
     * @Put('/mutate')
     * @Delete('/mutate')
     * @AuthMiddleware("\app\common\events\middleware\RequestMiddlewareEvent")
     */
    public function mutateAction()
    {
        try {
            $resolver = new MutationResolver($this);
            $this->sendResponse($resolver->resolve(), 200);
        } catch (\Throwable $exception) {
            $this->handleError($exception->getMessage(), $exception->getCode());
        }
    }
}
