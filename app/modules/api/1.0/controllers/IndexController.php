<?php
namespace Shop_categories\Modules\Api\Controllers;

use Shop_categories\Behaviors\AdjacencyListModelHelper;
use Shop_categories\RequestHandler\CreateRequestValidator;
use Shop_categories\RequestHandler\DeleteRequestValidator;
use Shop_categories\RequestHandler\GetRequestValidator;
use Shop_categories\RequestHandler\UpdateRequestValidator;
use JsonMapper_Exception;

/**
 * @RoutePrefix("/api/1.0/")
 */
class IndexController extends ControllerBase
{
    public function initialize()
    {
        $this->getService()::setVendorId('00492bc1-d22d-47b1-9372-8bc2ebf3c12d');
    }

    /**
     * Get all roots
     * @Get('roots')
     */
    public function rootsAction()
    {
        try {
            /** @var GetRequestValidator $request */
            $request = $this->getJsonMapper()->map(new \stdClass(), new GetRequestValidator());
            $request->successRequest($this->showPublicColumns($this->getService()->getRoots()));
        } catch (JsonMapper_Exception $exception) {
            $this->handleError($exception->getMessage(), 400);
        } catch (\Throwable $exception) {
            $this->handleError($exception->getMessage());
        }
    }

    /**
     * Get all categories related to vendor
     * @Get('/')
     */
    public function getAllAction()
    {
        try {
            //phpinfo(); exit;
            /** @var GetRequestValidator $request */
            $request = $this->getJsonMapper()->map(new \stdClass(), new GetRequestValidator());
            $request->successRequest($this->showPublicColumns($this->getService()->getAll()));
        } catch (JsonMapper_Exception $exception) {
            $this->handleError($exception->getMessage(), 400);
        } catch (\Throwable $exception) {
            $this->handleError($exception->getMessage(), 500);
        }
    }


    /**
     * Get category by Id
     * @Get('{categoryId:[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}}')
     * @param $categoryId
     */
    public function getAction($categoryId)
    {
        try {
            /** @var GetRequestValidator $request */
            $request = $this->getJsonMapper()->map(new \stdClass(), new GetRequestValidator());
            $category = $this->getService()->getCategory($categoryId);
            $request->successRequest($this->showPublicColumns($category));
        } catch (JsonMapper_Exception $exception) {
            $this->handleError($exception->getMessage(), 400);
        } catch (\Throwable $exception) {
            $this->handleError($exception->getMessage(), $exception->getCode());
        }
    }

    /**
     * Get category descendants
     * @Get('{id:[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}}/descendants')
     * @param $categoryId
     */
    public function descendantsAction($categoryId)
    {
        try {
            /** @var GetRequestValidator $request */
            $request = $this->getJsonMapper()->map(new \stdClass(), new GetRequestValidator());
            $descendants = $this->getService()->getDescendants($categoryId);
            $request->successRequest($this->showPublicColumns($descendants));
        } catch (JsonMapper_Exception $exception) {
            $this->handleError($exception->getMessage(), 400);
        } catch (\Throwable $exception) {
            $this->handleError($exception->getMessage(), $exception->getCode());
        }
    }

    /**
     * Get category children
     * @Get('{id:[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}}/children')
     * @param $categoryId
     */
    public function childrenAction($categoryId)
    {
        try {
            /** @var GetRequestValidator $request */
            $request = $this->getJsonMapper()->map(new \stdClass(), new GetRequestValidator());
            $children = $this->getService()->getChildren($categoryId);
            $request->successRequest($this->showPublicColumns($children));
        } catch (JsonMapper_Exception $exception) {
            $this->handleError($exception->getMessage(), 400);
        } catch (\Throwable $exception) {
            $this->handleError($exception->getMessage(), 500);
        }
    }

    /**
     * Get category parents
     * @Get('{id}/parents')
     * @param $categoryId
     */
    public function parentsAction($categoryId)
    {
        try {
            /** @var GetRequestValidator $request */
            $request = $this->getJsonMapper()->map(new \stdClass(), new GetRequestValidator());
            $parents = $this->getService()->getParents($categoryId);
            $request->successRequest($this->showPublicColumns($parents));
        } catch (JsonMapper_Exception $exception) {
            $this->handleError($exception->getMessage(), 400);
        } catch (\Throwable $exception) {
            $this->handleError($exception->getMessage(), 500);
        }
    }

    /**
     * Get category parent
     * @Get('{id:[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}}/parent')
     * @param $categoryId
     */
    public function parentAction($categoryId)
    {
        try {
            /** @var GetRequestValidator $request */
            $request = $this->getJsonMapper()->map(new \stdClass(), new GetRequestValidator());
            $parent = $this->getService()->getParent($categoryId);
            $request->successRequest($this->showPublicColumns($parent));
        } catch (JsonMapper_Exception $exception) {
            $this->handleError($exception->getMessage(), 400);
        } catch (\Throwable $exception) {
            $this->handleError($exception->getMessage());
        }
    }

    /**
     * Create category
     * Send response on success/fail
     * @Post('/')
     */
    public function createAction(): void
    {
        try {
            /** @var CreateRequestValidator $request */
            $request = $this->getJsonMapper()->map($this->request->getJsonRawBody(), new CreateRequestValidator());

            if (!$request->isValid()) {
                $request->invalidRequest();
            }

            $category = $this->getService()->save($request->toArray());

            $request->successRequest($this->showPublicColumns($category->toArray()));
        } catch (JsonMapper_Exception $exception) {
            $this->handleError($exception->getMessage(), 400);
        } catch (\Throwable $exception) {
            $this->handleError($exception->getMessage());
        }
    }

    /**
     * Update category
     * @Put('{id:[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}}')
     * @param $categoryId
     */
    public function updateAction($categoryId)
    {
        try {
            /** @var UpdateRequestValidator $request */
            $request = $this->getJsonMapper()->map($this->request->getJsonRawBody(), new UpdateRequestValidator());

            if (!$request->isValid()) {
                return $request->invalidRequest();
            }

            if ($category = $this->getService()->getCategoryFromRepository($categoryId)) {
                $category = $this->getService()->save($request->toArray(), $category);
            }

            $request->successRequest($this->showPublicColumns($category->toArray()));
        } catch (JsonMapper_Exception $exception) {
            $request->invalidRequest($exception->getMessage());
        } catch (\Throwable $exception) {
            $this->handleError($exception->getMessage());
        }
    }

    /**
     * Delete category
     * @Delete('{id:[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}}')
     * @param $categoryId
     */
    public function deleteAction($categoryId)
    {
        try {
            /** @var DeleteRequestValidator $request */
            $request = $this->getJsonMapper()->map(new \stdClass(), new DeleteRequestValidator());
            $this->getService()->delete($categoryId);
            $this->getService()->invalidateCache();
            $request->successRequest('Deleted');
        } catch (JsonMapper_Exception $exception) {
            $this->handleError($exception->getMessage(), 400);
        } catch (\Throwable $exception) {
            $this->handleError($exception->getMessage(), 500);
        }
    }
}
