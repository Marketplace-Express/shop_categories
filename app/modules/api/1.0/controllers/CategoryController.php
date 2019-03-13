<?php
namespace Shop_categories\Modules\Api\Controllers;

use Shop_categories\Controllers\BaseController;
use Shop_categories\RequestHandler\Category\{
    CreateRequestHandler,
    DeleteRequestHandler,
    GetRequestHandler,
    UpdateRequestHandler
};
use Shop_categories\Services\CategoryService;

/**
 * Class CategoryController
 * @package Shop_categories\Modules\Api\Controllers
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
     * Get all roots
     * @Get('/roots')
     */
    public function rootsAction()
    {
        try {
            /** @var GetRequestHandler $request */
            $request = $this->getJsonMapper()->map(new \stdClass(), new GetRequestHandler());
            $request->successRequest($this->getService()->getRoots());
        } catch (\Throwable $exception) {
            $this->handleError($exception->getMessage(), $exception->getCode() ?: 500);
        }
    }

    /**
     * Get all categories related to vendor
     * @Get('')
     */
    public function getAllAction()
    {
        try {
            /** @var GetRequestHandler $request */
            $request = $this->getJsonMapper()->map(new \stdClass(), new GetRequestHandler());
            $request->successRequest($this->toTree($this->getService()->getAll()));
        } catch (\Throwable $exception) {
            $this->handleError($exception->getMessage(), $exception->getCode() ?: 500);
        }
    }


    /**
     * Get category by Id
     * @Get('/{categoryId:[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}}')
     * @param $categoryId
     */
    public function getAction($categoryId)
    {
        try {
            /** @var GetRequestHandler $request */
            $request = $this->getJsonMapper()->map(new \stdClass(), new GetRequestHandler());
            $category = $this->getService()->getCategory($categoryId);
            $request->successRequest($category);
        } catch (\Throwable $exception) {
            $this->handleError($exception->getMessage(), $exception->getCode() ?: 500);
        }
    }

    /**
     * Get category descendants
     * @Get('/{id:[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}}/descendants')
     * @param $categoryId
     */
    public function descendantsAction($categoryId)
    {
        try {
            /** @var GetRequestHandler $request */
            $request = $this->getJsonMapper()->map(new \stdClass(), new GetRequestHandler());
            $descendants = $this->getService()->getDescendants($categoryId);
            $request->successRequest($this->toTree($descendants));
        } catch (\Throwable $exception) {
            $this->handleError($exception->getMessage(), $exception->getCode() ?: 500);
        }
    }

    /**
     * Get category children
     * @Get('/{id:[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}}/children')
     * @param $categoryId
     */
    public function childrenAction($categoryId)
    {
        try {
            /** @var GetRequestHandler $request */
            $request = $this->getJsonMapper()->map(new \stdClass(), new GetRequestHandler());
            $request->successRequest($this->getService()->getChildren($categoryId));
        } catch (\Throwable $exception) {
            $this->handleError($exception->getMessage(), $exception->getCode() ?: 500);
        }
    }

    /**
     * Get category parents
     * @Get('/{id}/parents')
     * @param $categoryId
     */
    public function parentsAction($categoryId)
    {
        try {
            /** @var GetRequestHandler $request */
            $request = $this->getJsonMapper()->map(new \stdClass(), new GetRequestHandler());
            $parents = $this->getService()->getParents($categoryId);
            $request->successRequest($this->toTree($parents));
        } catch (\Throwable $exception) {
            $this->handleError($exception->getMessage(), $exception->getCode() ?: 500);
        }
    }

    /**
     * Get category parent
     * @Get('/{id:[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}}/parent')
     * @param $categoryId
     */
    public function parentAction($categoryId)
    {
        try {
            /** @var GetRequestHandler $request */
            $request = $this->getJsonMapper()->map(new \stdClass(), new GetRequestHandler());
            $request->successRequest($this->getService()->getParent($categoryId));
        } catch (\Throwable $exception) {
            $this->handleError($exception->getMessage(), $exception->getCode() ?: 500);
        }
    }

    /**
     * Create category
     * Send response on success/fail
     * @Post('')
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
