<?php
/**
 * User: Wajdi Jurry
 * Date: 19/10/18
 * Time: 12:21 م
 */

namespace Shop_categories\Tests\Modules\Api\Controllers;

use PHPUnit\Framework\MockObject\MockObject;
use Shop_categories\Exceptions\ArrayOfStringsException;
use Shop_categories\Modules\Api\Controllers\IndexController;
use Shop_categories\RequestHandler\CreateRequestHandler;
use Shop_categories\RequestHandler\DeleteRequestHandler;
use Shop_categories\RequestHandler\GetRequestHandler;
use Shop_categories\RequestHandler\UpdateRequestHandler;
use Shop_categories\Services\CategoryService;

class IndexControllerTest extends \UnitTestCase
{
    const VENDOR_ID = 'd9423ce9-bf98-432b-bb00-1c31d4e50e14';
    const CATEGORY_ID = '92aaf1e3-ed52-44ad-ac2a-f7c8d2b08a6d';

    public $jsonRawBody;
    public function setUp()
    {
        parent::setUp();
        $this->jsonRawBody = new \stdClass();
        $this->jsonRawBody->name = 'sample category';
        $this->jsonRawBody->parentId = 'a06c62f3-9398-438f-a563-f188f509b577';
    }

    public function getControllerMock(...$methods)
    {
        return $this->getMockBuilder(IndexController::class)
            ->disableOriginalConstructor()
            ->setMethods($methods)
            ->getMock();
    }

    public function getJsonMapperMock(...$methods)
    {
        return $this->getMockBuilder(\JsonMapper::class)
            ->setMethods($methods)
            ->getMock();
    }

    public function getGetRequestHandlerMock(...$methods)
    {
        return $this->getMockBuilder(GetRequestHandler::class)
            ->setMethods($methods)
            ->getMock();
    }

    public function getCreateRequestHandlerMock(...$methods)
    {
        return $this->getMockBuilder(CreateRequestHandler::class)
            ->setMethods($methods)
            ->getMock();
    }

    public function getUpdateRequestHandlerMock(...$methods)
    {
        return $this->getMockBuilder(CreateRequestHandler::class)
            ->setMethods($methods)
            ->getMock();
    }

    public function getDeleteRequestHandlerMock(...$methods)
    {
        return $this->getMockBuilder(DeleteRequestHandler::class)
            ->setMethods($methods)
            ->getMock();
    }

    public function getServiceMock(...$methods)
    {
        return $this->getMockBuilder(CategoryService::class)
            ->setMethods($methods)
            ->getMock();
    }

    public function testRootsAction()
    {
        /** @var IndexController|MockObject $controllerMock */
        $controllerMock = $this->getControllerMock('nothing');

        /** @var GetRequestHandler|MockObject $getGetRequestHandlerMock */
        $getGetRequestHandlerMock = $this->getGetRequestHandlerMock('successRequest');
        $getGetRequestHandlerMock->expects(self::once())->method('successRequest');

        /** @var \JsonMapper|MockObject $jsonMapperMock */
        $jsonMapperMock = $this->getJsonMapperMock('map');
        $jsonMapperMock->expects($this->once())->method('map')->with(new \stdClass(), new GetRequestHandler())->willReturn($getGetRequestHandlerMock);
        $controllerMock->setJsonMapper($jsonMapperMock);

        /** @var CategoryService|MockObject $serviceMock */
        $serviceMock = $this->getServiceMock('getRoots');
        $serviceMock->expects(self::once())->method('getRoots')->willReturn([]);
        $controllerMock->setService($serviceMock);

        $controllerMock->rootsAction();
    }

    public function testRootsActionWithException()
    {
        /** @var IndexController|MockObject $controllerMock */
        $controllerMock = $this->getControllerMock('handleError');

        /** @var GetRequestHandler|MockObject $getGetRequestHandlerMock */
        $getGetRequestHandlerMock = $this->getGetRequestHandlerMock();

        /** @var \JsonMapper|MockObject $jsonMapperMock */
        $jsonMapperMock = $this->getJsonMapperMock('map');
        $jsonMapperMock->expects(self::once())->method('map')->with(new \stdClass(), new GetRequestHandler())->willReturn($getGetRequestHandlerMock);
        $controllerMock->setJsonMapper($jsonMapperMock);

        /** @var CategoryService|MockObject $serviceMock */
        $serviceMock = $this->getServiceMock('getRoots');
        $serviceMock->expects(self::once())->method('getRoots')->willThrowException(new \Exception('No categories found', 404));
        $controllerMock->setService($serviceMock);

        $controllerMock->expects(self::once())->method('handleError')->with('No categories found', 404);
        $controllerMock->rootsAction();
    }

    public function testDescendantsAction()
    {
        /** @var IndexController|MockObject $controllerMock */
        $controllerMock = $this->getControllerMock('toTree');

        /** @var GetRequestHandler|MockObject $getRequestHandlerMock */
        $getRequestHandlerMock = $this->getGetRequestHandlerMock('successRequest');
        $getRequestHandlerMock->expects(self::once())->method('successRequest');

        /** @var \JsonMapper|MockObject $jsonMapperMock */
        $jsonMapperMock = $this->getJsonMapperMock('map');
        $jsonMapperMock->expects(self::once())->method('map')->with(new \stdClass(), new GetRequestHandler())->willReturn($getRequestHandlerMock);
        $controllerMock->setJsonMapper($jsonMapperMock);

        /** @var CategoryService|MockObject $serviceMock */
        $serviceMock = $this->getServiceMock('getDescendants');
        $serviceMock->expects(self::once())->method('getDescendants')->with(self::CATEGORY_ID)->willReturn([]);
        $controllerMock->setService($serviceMock);

        $controllerMock->expects(self::once())->method('toTree')->with([])->willReturn([]);
        $controllerMock->descendantsAction(self::CATEGORY_ID);
    }

    public function testDescendantsActionWithException()
    {
        /** @var IndexController|MockObject $controllerMock */
        $controllerMock = $this->getControllerMock('handleError');

        /** @var GetRequestHandler|MockObject $getRequestHandlerMock */
        $getRequestHandlerMock = $this->getGetRequestHandlerMock();

        /** @var \JsonMapper|MockObject $jsonMapperMock */
        $jsonMapperMock = $this->getJsonMapperMock('map');
        $jsonMapperMock->expects(self::once())->method('map')->with(new \stdClass(), new GetRequestHandler())->willReturn($getRequestHandlerMock);
        $controllerMock->setJsonMapper($jsonMapperMock);

        /** @var CategoryService|MockObject $serviceMock */
        $serviceMock = $this->getServiceMock('getDescendants');
        $serviceMock->expects(self::once())->method('getDescendants')->willThrowException(new \Exception('Category not found or maybe deleted', 404));
        $controllerMock->setService($serviceMock);

        $controllerMock->expects(self::once())->method('handleError')->with('Category not found or maybe deleted', 404);
        $controllerMock->descendantsAction(self::CATEGORY_ID);
    }

    public function testGetAction()
    {
        /** @var IndexController|MockObject $controllerMock */
        $controllerMock = $this->getControllerMock('nothing');

        /** @var GetRequestHandler|MockObject $getRequestHandlerMock */
        $getRequestHandlerMock = $this->getGetRequestHandlerMock('successRequest');
        $getRequestHandlerMock->expects(self::once())->method('successRequest');

        /** @var \JsonMapper|MockObject $jsonMapperMock */
        $jsonMapperMock = $this->getJsonMapperMock('map');
        $jsonMapperMock->expects(self::once())->method('map')->with(new \stdClass(), new GetRequestHandler())->willReturn($getRequestHandlerMock);
        $controllerMock->setJsonMapper($jsonMapperMock);

        /** @var CategoryService|MockObject $serviceMock */
        $serviceMock = $this->getServiceMock('getCategory');
        $serviceMock->expects(self::once())->method('getCategory')->with(self::CATEGORY_ID)->willReturn([]);
        $controllerMock->setService($serviceMock);

        $controllerMock->getAction(self::CATEGORY_ID);
    }

    public function testGetActionWithException()
    {
        /** @var IndexController|MockObject $controllerMock */
        $controllerMock = $this->getControllerMock('handleError');

        /** @var GetRequestHandler|MockObject $getRequestHandlerMock */
        $getRequestHandlerMock = $this->getGetRequestHandlerMock();

        /** @var \JsonMapper|MockObject $jsonMapperMock */
        $jsonMapperMock = $this->getJsonMapperMock('map');
        $jsonMapperMock->expects(self::once())->method('map')->with(new \stdClass(), new GetRequestHandler())->willReturn($getRequestHandlerMock);
        $controllerMock->setJsonMapper($jsonMapperMock);

        /** @var CategoryService|MockObject $serviceMock */
        $serviceMock = $this->getServiceMock('getCategory');
        $serviceMock->expects(self::once())->method('getCategory')->willThrowException(new \Exception('Category not found or maybe deleted', 404));
        $controllerMock->setService($serviceMock);

        $controllerMock->expects(self::once())->method('handleError')->with('Category not found or maybe deleted', 404);
        $controllerMock->getAction(self::CATEGORY_ID);
    }

    public function testParentAction()
    {
        /** @var IndexController|MockObject $controllerMock */
        $controllerMock = $this->getControllerMock('nothing');

        /** @var GetRequestHandler|MockObject $getRequestHandlerMock */
        $getRequestHandlerMock = $this->getGetRequestHandlerMock('successRequest');
        $getRequestHandlerMock->expects(self::once())->method('successRequest');

        /** @var \JsonMapper|MockObject $jsonMapperMock */
        $jsonMapperMock = $this->getJsonMapperMock('map');
        $jsonMapperMock->expects(self::once())->method('map')->with(new \stdClass(), new GetRequestHandler())->willReturn($getRequestHandlerMock);
        $controllerMock->setJsonMapper($jsonMapperMock);

        /** @var CategoryService|MockObject $serviceMock */
        $serviceMock = $this->getServiceMock('getParent');
        $serviceMock->expects(self::once())->method('getParent')->with(self::CATEGORY_ID)->willReturn([]);
        $controllerMock->setService($serviceMock);

        $controllerMock->parentAction(self::CATEGORY_ID);
    }

    public function testParentActionWithException()
    {
        /** @var IndexController|MockObject $controllerMock */
        $controllerMock = $this->getControllerMock('handleError');

        /** @var GetRequestHandler|MockObject $getRequestHandlerMock */
        $getRequestHandlerMock = $this->getGetRequestHandlerMock();

        /** @var \JsonMapper|MockObject $jsonMapperMock */
        $jsonMapperMock = $this->getJsonMapperMock('map');
        $jsonMapperMock->expects(self::once())->method('map')->with(new \stdClass(), new GetRequestHandler())->willReturn($getRequestHandlerMock);
        $controllerMock->setJsonMapper($jsonMapperMock);

        /** @var CategoryService|MockObject $serviceMock */
        $serviceMock = $this->getServiceMock('getParent');
        $serviceMock->expects(self::once())->method('getParent')->willThrowException(new \Exception('Category not found or maybe deleted', 404));
        $controllerMock->setService($serviceMock);

        $controllerMock->expects(self::once())->method('handleError')->with('Category not found or maybe deleted', 404);
        $controllerMock->parentAction(self::CATEGORY_ID);
    }

    public function testParentsAction()
    {
        /** @var IndexController|MockObject $controllerMock */
        $controllerMock = $this->getControllerMock('toTree');

        /** @var GetRequestHandler|MockObject $getRequestHandlerMock */
        $getRequestHandlerMock = $this->getGetRequestHandlerMock('successRequest');
        $getRequestHandlerMock->expects(self::once())->method('successRequest');

        /** @var \JsonMapper|MockObject $jsonMapperMock */
        $jsonMapperMock = $this->getJsonMapperMock('map');
        $jsonMapperMock->expects(self::once())->method('map')->with(new \stdClass(), new GetRequestHandler())->willReturn($getRequestHandlerMock);
        $controllerMock->setJsonMapper($jsonMapperMock);

        /** @var CategoryService|MockObject $serviceMock */
        $serviceMock = $this->getServiceMock('getParents');
        $serviceMock->expects(self::once())->method('getParents')->with(self::CATEGORY_ID)->willReturn([]);
        $controllerMock->setService($serviceMock);

        $controllerMock->expects(self::once())->method('toTree')->with([])->willReturn([]);
        $controllerMock->parentsAction(self::CATEGORY_ID);
    }

    public function testParentsActionWithException()
    {
        /** @var IndexController|MockObject $controllerMock */
        $controllerMock = $this->getControllerMock('handleError');

        /** @var GetRequestHandler|MockObject $getRequestHandlerMock */
        $getRequestHandlerMock = $this->getGetRequestHandlerMock();

        /** @var \JsonMapper|MockObject $jsonMapperMock */
        $jsonMapperMock = $this->getJsonMapperMock('map');
        $jsonMapperMock->expects(self::once())->method('map')->with(new \stdClass(), new GetRequestHandler())->willReturn($getRequestHandlerMock);
        $controllerMock->setJsonMapper($jsonMapperMock);

        /** @var CategoryService|MockObject $serviceMock */
        $serviceMock = $this->getServiceMock('getParents');
        $serviceMock->expects(self::once())->method('getParents')->willThrowException(new \Exception('Category not found or maybe deleted', 404));
        $controllerMock->setService($serviceMock);

        $controllerMock->expects(self::once())->method('handleError')->with('Category not found or maybe deleted', 404);
        $controllerMock->parentsAction(self::CATEGORY_ID);
    }

    public function testGetAllAction()
    {
        /** @var IndexController|MockObject $controllerMock */
        $controllerMock = $this->getControllerMock('toTree');
        $controllerMock->expects(self::once())->method('toTree')->with([])->willReturn([]);

        /** @var GetRequestHandler|MockObject $getRequestHandlerMock */
        $getRequestHandlerMock = $this->getGetRequestHandlerMock('successRequest');
        $getRequestHandlerMock->expects(self::once())->method('successRequest');

        /** @var \JsonMapper|MockObject $jsonMapperMock */
        $jsonMapperMock = $this->getJsonMapperMock('map');
        $jsonMapperMock->expects(self::once())->method('map')->with(new \stdClass(), new GetRequestHandler())->willReturn($getRequestHandlerMock);
        $controllerMock->setJsonMapper($jsonMapperMock);

        /** @var CategoryService|MockObject $serviceMock */
        $serviceMock = $this->getServiceMock('getAll');
        $serviceMock->expects(self::once())->method('getAll')->willReturn([]);
        $controllerMock->setService($serviceMock);

        $controllerMock->getAllAction();
    }

    public function testGetAllActionWithException()
    {
        /** @var IndexController|MockObject $controllerMock */
        $controllerMock = $this->getControllerMock('handleError');

        /** @var GetRequestHandler|MockObject $getRequestHandlerMock */
        $getRequestHandlerMock = $this->getGetRequestHandlerMock();

        /** @var \JsonMapper|MockObject $jsonMapperMock */
        $jsonMapperMock = $this->getJsonMapperMock('map');
        $jsonMapperMock->expects(self::once())->method('map')->with(new \stdClass(), new GetRequestHandler())->willReturn($getRequestHandlerMock);
        $controllerMock->setJsonMapper($jsonMapperMock);

        /** @var CategoryService|MockObject $serviceMock */
        $serviceMock = $this->getServiceMock('getAll');
        $serviceMock->expects(self::once())->method('getAll')->willThrowException(new \Exception('No categories found', 404));
        $controllerMock->setService($serviceMock);

        $controllerMock->expects(self::once())->method('handleError')->with('No categories found', 404);
        $controllerMock->getAllAction();
    }

    public function testChildrenAction()
    {

        /** @var IndexController|MockObject $controllerMock */
        $controllerMock = $this->getControllerMock('nothing');

        /** @var GetRequestHandler|MockObject $getRequestHandlerMock */
        $getRequestHandlerMock = $this->getGetRequestHandlerMock('successRequest');
        $getRequestHandlerMock->expects(self::once())->method('successRequest');

        /** @var \JsonMapper|MockObject $jsonMapperMock */
        $jsonMapperMock = $this->getJsonMapperMock('map');
        $jsonMapperMock->expects(self::once())->method('map')->with(new \stdClass(), new GetRequestHandler())->willReturn($getRequestHandlerMock);
        $controllerMock->setJsonMapper($jsonMapperMock);

        /** @var CategoryService|MockObject $serviceMock */
        $serviceMock = $this->getServiceMock('getChildren');
        $serviceMock->expects(self::once())->method('getChildren')->with(self::CATEGORY_ID)->willReturn([]);
        $controllerMock->setService($serviceMock);

        $controllerMock->childrenAction(self::CATEGORY_ID);
    }

    public function testChildrenActionWithException()
    {
        /** @var IndexController|MockObject $controllerMock */
        $controllerMock = $this->getControllerMock('handleError');

        /** @var GetRequestHandler|MockObject $getRequestHandlerMock */
        $getRequestHandlerMock = $this->getGetRequestHandlerMock();

        /** @var \JsonMapper|MockObject $jsonMapperMock */
        $jsonMapperMock = $this->getJsonMapperMock('map');
        $jsonMapperMock->expects(self::once())->method('map')->with(new \stdClass(), new GetRequestHandler())->willReturn($getRequestHandlerMock);
        $controllerMock->setJsonMapper($jsonMapperMock);

        /** @var CategoryService|MockObject $serviceMock */
        $serviceMock = $this->getServiceMock('getChildren');
        $serviceMock->expects(self::once())->method('getChildren')->with(self::CATEGORY_ID)->willThrowException(new \Exception('Category not found or maybe deleted', 404));
        $controllerMock->setService($serviceMock);

        $controllerMock->expects(self::once())->method('handleError')->with('Category not found or maybe deleted', 404);
        $controllerMock->childrenAction(self::CATEGORY_ID);
    }

    public function testCreateAction()
    {
        /** @var IndexController|MockObject $controllerMock */
        $controllerMock = $this->getControllerMock('nothing');

        $request = $this->di->get('request');
        $request->setJsonRawBody($this->jsonRawBody);

        /** @var CreateRequestHandler|MockObject $createRequestHandlerMock */
        $createRequestHandlerMock = $this->getCreateRequestHandlerMock('isValid', 'toArray', 'successRequest');
        $createRequestHandlerMock->expects(self::once())->method('isValid')->willReturn(true);
        $createRequestHandlerMock->expects(self::once())->method('toArray')->willReturn((array) $request->getJsonRawBody());
        $createRequestHandlerMock->expects(self::once())->method('successRequest');

        /** @var \JsonMapper|MockObject $jsonMapperMock */
        $jsonMapperMock = $this->getJsonMapperMock('map');
        $jsonMapperMock->expects(self::once())->method('map')->with($request->getJsonRawBody(), new CreateRequestHandler())->willReturn($createRequestHandlerMock);
        $controllerMock->setJsonMapper($jsonMapperMock);

        /** @var CategoryService|MockObject $serviceMock */
        $serviceMock = $this->getServiceMock('create');
        $serviceMock->expects(self::once())->method('create')->with((array) $request->getJsonRawBody())->willReturn((array) $request->getJsonRawBody());
        $controllerMock->setService($serviceMock);

        $controllerMock->createAction();
    }

    public function testCreateActionWithInvalidRequest()
    {
        /** @var IndexController|MockObject $controllerMock */
        $controllerMock = $this->getControllerMock('handleError');

        $request = $this->di->get('request');
        $request->setJsonRawBody($this->jsonRawBody);

        /** @var CreateRequestHandler|MockObject $createRequestHandlerMock */
        $createRequestHandlerMock = $this->getCreateRequestHandlerMock('isValid', 'invalidRequest');
        $createRequestHandlerMock->expects(self::once())->method('isValid')->willReturn(false);
        $createRequestHandlerMock->expects(self::once())->method('invalidRequest')->willThrowException(new ArrayOfStringsException(['Invalid request'], 400));

        /** @var \JsonMapper|MockObject $jsonMapperMock */
        $jsonMapperMock = $this->getJsonMapperMock('map');
        $jsonMapperMock->expects(self::once())->method('map')->with($request->getJsonRawBody(), new CreateRequestHandler())->willReturn($createRequestHandlerMock);
        $controllerMock->setJsonMapper($jsonMapperMock);

        $controllerMock->expects(self::once())->method('handleError')->with(json_encode(['Invalid request']), 400);
        $controllerMock->createAction();
    }

    public function testUpdateAction()
    {
        /** @var IndexController|MockObject $controllerMock */
        $controllerMock = $this->getControllerMock('nothing');

        $request = $this->di->get('request');
        $request->setJsonRawBody($this->jsonRawBody);

        /** @var UpdateRequestHandler|MockObject $updateRequestHandlerMock */
        $updateRequestHandlerMock = $this->getUpdateRequestHandlerMock('isValid', 'toArray', 'successRequest');
        $updateRequestHandlerMock->expects(self::once())->method('isValid')->willReturn(true);
        $updateRequestHandlerMock->expects(self::once())->method('toArray')->willReturn((array) $request->getJsonRawBody());
        $updateRequestHandlerMock->expects(self::once())->method('successRequest');

        /** @var \JsonMapper|MockObject $jsonMapperMock */
        $jsonMapperMock = $this->getJsonMapperMock('map');
        $jsonMapperMock->expects(self::once())->method('map')->with($request->getJsonRawBody(), new UpdateRequestHandler())->willReturn($updateRequestHandlerMock);
        $controllerMock->setJsonMapper($jsonMapperMock);

        /** @var CategoryService|MockObject $serviceMock */
        $serviceMock = $this->getServiceMock('update');
        $serviceMock->expects(self::once())->method('update')->with(self::CATEGORY_ID, (array) $request->getJsonRawBody())->willReturn((array) $request->getJsonRawBody());
        $controllerMock->setService($serviceMock);

        $controllerMock->updateAction(self::CATEGORY_ID);
    }

    public function testUpdateActionWithInvalidRequest()
    {
        /** @var IndexController|MockObject $controllerMock */
        $controllerMock = $this->getControllerMock('handleError');

        $request = $this->di->get('request');
        $request->setJsonRawBody($this->jsonRawBody);

        $updateRequestHandlerMock = $this->getUpdateRequestHandlerMock('isValid', 'invalidRequest');
        $updateRequestHandlerMock->expects(self::once())->method('isValid')->willReturn(false);
        $updateRequestHandlerMock->expects(self::once())->method('invalidRequest')->willThrowException(new ArrayOfStringsException(['Invalid request'], 400));

        /** @var \JsonMapper|MockObject $jsonMapperMock */
        $jsonMapperMock = $this->getJsonMapperMock('map');
        $jsonMapperMock->expects(self::once())->method('map')->with($request->getJsonRawBody(), new UpdateRequestHandler())->willReturn($updateRequestHandlerMock);
        $controllerMock->setJsonMapper($jsonMapperMock);

        $controllerMock->expects(self::once())->method('handleError')->with(json_encode(['Invalid request']), 400);
        $controllerMock->updateAction(self::CATEGORY_ID);
    }

    public function testDeleteAction()
    {
        /** @var IndexController|MockObject $controllerMock */
        $controllerMock = $this->getControllerMock('nothing');

        $deleteRequestHandlerMock = $this->getDeleteRequestHandlerMock('successRequest');
        $deleteRequestHandlerMock->expects(self::once())->method('successRequest');

        /** @var \JsonMapper|MockObject $jsonMapperMock */
        $jsonMapperMock = $this->getJsonMapperMock('map');
        $jsonMapperMock->expects(self::once())->method('map')->with(new \stdClass(), new DeleteRequestHandler())->willReturn($deleteRequestHandlerMock);
        $controllerMock->setJsonMapper($jsonMapperMock);

        /** @var CategoryService|MockObject $serviceMock */
        $serviceMock = $this->getServiceMock('delete');
        $serviceMock->expects(self::once())->method('delete')->with(self::CATEGORY_ID);
        $controllerMock->setService($serviceMock);

        $controllerMock->deleteAction(self::CATEGORY_ID);
    }

    public function testDeleteActionWithException()
    {
        /** @var IndexController|MockObject $controllerMock */
        $controllerMock = $this->getControllerMock('handleError');

        /** @var CategoryService|MockObject $serviceMock */
        $serviceMock = $this->getServiceMock('delete');
        $serviceMock->expects(self::once())->method('delete')->with(self::CATEGORY_ID)->willThrowException(new \Exception('Category not found or maybe deleted', 404));
        $controllerMock->setService($serviceMock);

        $controllerMock->expects(self::once())->method('handleError')->with('Category not found or maybe deleted', 404);
        $controllerMock->deleteAction(self::CATEGORY_ID);
    }
}
