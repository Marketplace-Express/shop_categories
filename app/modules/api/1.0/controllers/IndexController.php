<?php
namespace Shop_categories\Modules\Api\Controllers;

use Phalcon\Exception;
use Phalcon\Http\Response;
use Phalcon\Mvc\Model;
use Phalcon\Mvc\ModelInterface;
use Ramsey\Uuid\Uuid;
use Shop_categories\Repositories\CategoryRepository;
use Shop_categories\Validators\CreateRequestValidator;
use Shop_categories\Validators\DeleteRequestValidator;
use Shop_categories\Validators\GetRequestValidator;
use Shop_categories\Validators\UpdateRequestValidator;

/**
 * @RoutePrefix("/api/1.0/")
 */
class IndexController extends ControllerBase
{
    /**
     * Get all roots
     * @Get('roots')
     * @throws \JsonMapper_Exception
     */
    public function rootsAction()
    {
        /**
         * @var GetRequestValidator $request
         */
        $request = $this->jsonMapper->map(new \stdClass(), new GetRequestValidator());

        /**
         * @var CategoryRepository $roots
         */
        if ($roots = CategoryRepository::getRoots()) {
            return $request->successRequest($this->showPublicColumns($roots->toArray()));
        } else {
            return $request->notFound();
        }
    }


    /**
     * Get category by Id
     * @Get('{id:[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}}')
     * @param $id
     * @return Response|\Phalcon\Http\ResponseInterface
     * @throws \JsonMapper_Exception
     */
    public function getAction($id)
    {
        /**
         * @var GetRequestValidator $request
         */
        $request = $this->jsonMapper->map(new \stdClass(), new GetRequestValidator());

        /**
         * @var CategoryRepository $category
         */
        if ($category = CategoryRepository::findById($id)) {
            return $request->successRequest($this->showPublicColumns($category->toArray()));
        } else {
            return $request->notFound();
        }
    }

    /**
     * Get category descendants
     * @Get('{id:[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}}/descendants')
     * @param $id
     * @return Response|\Phalcon\Http\ResponseInterface
     * @throws \JsonMapper_Exception
     */
    public function descendantsAction($id)
    {
        /**
         * @var GetRequestValidator $request
         */
        $request = $this->jsonMapper->map(new \stdClass(), new GetRequestValidator());

        /**
         * @var Model\Behavior\NestedSet $descendants
         */
        if ($descendants = CategoryRepository::getDescendants($id)) {
            return $request->successRequest($this->showPublicColumns($descendants->toTree()));
        } else {
            return $request->notFound();
        }
    }

    /**
     * Get category children
     * @Get('{id:[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}}/children')
     * @param $id
     * @return Response|\Phalcon\Http\ResponseInterface
     * @throws \JsonMapper_Exception
     */
    public function childrenAction($id)
    {
        /***
         * @var GetRequestValidator $request
         */
        $request = $this->jsonMapper->map(new \stdClass(), new GetRequestValidator());

        /**
         * @var Model\Behavior\NestedSet $children
         */
        if ($children = CategoryRepository::getChildren($id)) {
            return $request->successRequest($this->showPublicColumns($children->toTree()));
        } else {
            return $request->notFound();
        }
    }

    /**
     * Get category parents
     * @Get('{id:[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}}/parents')
     * @param $id
     * @return Response|\Phalcon\Http\ResponseInterface
     * @throws \JsonMapper_Exception
     */
    public function parentsAction($id)
    {
        /**
         * @var GetRequestValidator $request
         */
        $request = $this->jsonMapper->map(new \stdClass(), new GetRequestValidator());

        /**
         * @var Model\Behavior\NestedSet $parents
         */
        $parents = CategoryRepository::getParents($id);

        if ($parents) {
            return $request->successRequest($parents->toTree());
        } else {
            return $request->notFound();
        }
    }

    /**
     * Get category parent
     * @Get('{id:[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}}/parent')
     * @param $id
     * @return Response|\Phalcon\Http\ResponseInterface
     * @throws \JsonMapper_Exception
     */
    public function parentAction($id)
    {
        /**
         * @var GetRequestValidator $request
         */
        $request = $this->jsonMapper->map(new \stdClass(), new GetRequestValidator());

        /**
         * @var Model\Behavior\NestedSet $parent
         */
        $parent = CategoryRepository::getParents($id, 1);

        if ($parent) {
            return $request->successRequest($parent->toTree());
        } else {
            return $request->notFound();
        }
    }

    /**
     * Create category
     * @Post('/')
     */
    public function createAction()
    {
        try {
            /** @var UpdateRequestValidator $request */
            $request = $this->jsonMapper->map($this->request->getJsonRawBody(), new CreateRequestValidator());

            if (!$request->isValid()) {
                return $request->invalidRequest();
            }

            /**
             * @var Model\Behavior\NestedSet $category
             */
            $category = new CategoryRepository();

            $data['categoryId'] = Uuid::uuid4()->toString();
            $data['categoryName'] = $request->getName();

            if ($request->getParentId()) {
                /**
                 * @var $parent ModelInterface
                 */
                $parent = CategoryRepository::findById($request->getParentId());
                if (!$parent) {
                    return $request->notFound('Parent category not found!');
                }
                try {
                    $category->appendTo($parent, $data);
                } catch (\Exception $exception) {
                    return $this->handleServerError($exception->getMessage());
                }
            }

            $isCreated = $category->saveNode($data);

            if (!$isCreated) {
                $errors = array_map(function (
                    /**
                     * @var Exception $message
                     */
                    $message
                ){
                    return $message->getMessage();
                }, $category->getMessages());

                return $this->handleServerError($errors);
            }

            $category = $this->showPublicColumns($category->toArray());
            return $request->successRequest($category);

        } catch (\JsonMapper_Exception $exception) {

            return $request->invalidRequest($exception->getMessage());

        } catch (\Exception $exception) {

            return $this->handleServerError($exception->getMessage());

        }
    }

    /**
     * Update category
     * @Put('{id:[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}}')
     * @param $id
     * @return Response
     */
    public function updateAction($id)
    {
        try {
            /** @var UpdateRequestValidator $request */
            $request = $this->jsonMapper->map($this->request->getJsonRawBody(), new UpdateRequestValidator());

            if (!$request->isValid()) {
                return $request->invalidRequest();
            }

            /**
             * Find category
             * @var Model\Behavior\NestedSet $category
             */
            $category = CategoryRepository::findById($id);

            // Check if category exists
            if (!$category) {
                return $request->notFound();
            }

            // Initialize "fields to be updated" array
            $data = [];

            if ($request->getName()) {
                $data['categoryName'] = $request->getName();
            }

            if ($request->getParentId()) {
                /**
                 * @var ModelInterface $parent
                 */
                $parent = CategoryRepository::findById($request->getParentId());
                if (!$parent) {
                    return $request->notFound('Parent category not found!');
                }

                // Move category to last position at parent
                $category->moveAsLast($parent);

                if (!$data) {
                    // If no fields to be updated, return success
                    $category = $this->showPublicColumns($category->toArray());
                    return $request->successRequest($category);
                }
            }

            if ($data) {
                // If there are remaining fields to be updated
                if (!$category->saveNode($data)) {
                    $errors = array_map(function (
                        /** @var Exception $message **/
                        $message
                    ) {
                        return $message->getMessage();
                    }, $category->getMessages());
                    return $this->handleServerError($errors);
                }

                $category = $this->showPublicColumns($category->toArray());
                return $request->successRequest($category);

            } else {
                return $request->invalidRequest('Nothing to be updated!');
            }

        } catch (\JsonMapper_Exception $exception) {

            return $request->invalidRequest($exception->getMessage());

        } catch (\Exception $exception) {

            return $this->handleServerError($exception->getMessage());

        }
    }

    /**
     * Delete category
     * @Delete('{id:[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}}')
     * @param $id
     * @return Response|\Phalcon\Http\ResponseInterface
     */
    public function deleteAction($id)
    {
        try {
            /**
             * @var DeleteRequestValidator $request
             */
            $request = $this->jsonMapper->map(new \stdClass(), new DeleteRequestValidator());

            /**
             * @var Model\Behavior\NestedSet $category
             */
            if ($category = CategoryRepository::findById($id)) {
                $category->deleteNode();
                return $request->successRequest('Deleted!');
            } else {
                return $request->notFound();
            }
        } catch (\Exception $exception) {

            return $this->handleServerError($exception->getMessage());

        }
    }
}