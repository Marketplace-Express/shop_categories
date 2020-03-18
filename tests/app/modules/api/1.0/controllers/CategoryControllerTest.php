<?php
/**
 * User: Wajdi Jurry
 * Date: 19/10/18
 * Time: 12:21 م
 */

namespace tests\app\modules\api\controllers;

use app\common\requestHandler\FetchRequestHandler;
use app\common\requestHandler\MutationResolver;
use PHPUnit\Framework\MockObject\MockObject;
use app\modules\api\controllers\CategoryController;
use stdClass;
use JsonMapper;

class CategoryControllerTest extends \UnitTestCase
{
    const VENDOR_ID = 'd9423ce9-bf98-432b-bb00-1c31d4e50e14';
    const CATEGORY_ID = '92aaf1e3-ed52-44ad-ac2a-f7c8d2b08a6d';

    public $jsonRawBody;
    public function setUp()
    {
        parent::setUp();
        $this->jsonRawBody = new stdClass();
        $this->jsonRawBody->name = 'sample category';
        $this->jsonRawBody->parentId = 'a06c62f3-9398-438f-a563-f188f509b577';
    }

    public function getControllerMock(...$methods)
    {
        return $this->getMockBuilder(CategoryController::class)
            ->disableOriginalConstructor()
            ->setMethods($methods)
            ->getMock();
    }

    public function getJsonMapperMock(...$methods)
    {
        return $this->getMockBuilder(JsonMapper::class)
            ->setMethods($methods)
            ->getMock();
    }

    public function getFetchRequestHandlerMock(CategoryController $controller, ...$methods)
    {
        return $this->getMockBuilder(FetchRequestHandler::class)
            ->setConstructorArgs([$controller])
            ->setMethods($methods)
            ->getMock();
    }

    public function getMutationResolverMock(...$methods)
    {
        return $this->getMockBuilder(MutationResolver::class)
            ->setMethods($methods)
            ->getMock();
    }

    public function testFetchAction()
    {
        /** @var CategoryController|MockObject $controllerMock */
        $controllerMock = $this->getControllerMock('nothing');

        /** @var FetchRequestHandler|MockObject $fetchRequestHandlerMock */
        $fetchRequestHandlerMock = $this->getFetchRequestHandlerMock($controllerMock, 'successRequest', 'toArray');
        $fetchRequestHandlerMock->expects(self::once())->method('toArray')->willReturn([]);
        $fetchRequestHandlerMock->expects(self::once())->method('successRequest');

        /** @var \JsonMapper|MockObject $jsonMapperMock */
        $jsonMapperMock = $this->getJsonMapperMock('map');
        $jsonMapperMock->expects($this->once())->method('map')->with(new \stdClass(), $fetchRequestHandlerMock)->willReturn($fetchRequestHandlerMock);
        $controllerMock->setJsonMapper($jsonMapperMock);

        $controllerMock->fetchAction($fetchRequestHandlerMock);
    }
}
