<?php
/**
 * User: Wajdi Jurry
 * Date: 19/10/18
 * Time: 12:21 م
 */

namespace tests\app\modules\api\controllers;


use tests\mocks\ResponseMock;
use tests\UnitTestCase;
use app\common\requestHandler\FetchRequestHandler;
use app\common\requestHandler\MutationResolver;
use PHPUnit\Framework\MockObject\MockObject;
use app\modules\api\controllers\CategoryController;
use tests\mocks\RequestMock;

class CategoryControllerTest extends UnitTestCase
{
    const store_id = 'd9423ce9-bf98-432b-bb00-1c31d4e50e14';
    const CATEGORY_ID = '92aaf1e3-ed52-44ad-ac2a-f7c8d2b08a6d';

    /** @var CategoryController */
    private $controller;

    public function setUp()
    {
        $this->controller = new CategoryController();
        parent::setUp();
    }

    public function getControllerMock(...$methods)
    {
        return $this->getMockBuilder(CategoryController::class)
            ->disableOriginalConstructor()
            ->setMethods($methods)
            ->getMock();
    }

    public function getFetchRequestHandlerMock(...$methods)
    {
        return $this->getMockBuilder(FetchRequestHandler::class)
            ->setMethods($methods)
            ->getMock();
    }

    public function getMutationResolverMock(...$methods)
    {
        return $this->getMockBuilder(MutationResolver::class)
            ->setMethods($methods)
            ->getMock();
    }

    public function testFetchActionAll()
    {
        /** @var ResponseMock $response */
        $response = $this->di->get('response');

        /** @var RequestMock $request */
        $request = $this->di->get('request');
        $request->setJsonRawBody(json_decode(json_encode([
            'query' => '{
                            categories {
                                id
                                name
                            }
                        }',
            'variables' => [
                'storeId' => self::store_id
            ]
        ])));

        $this->controller->fetchAction(new FetchRequestHandler());

        $this->assertEquals([
            'status' => 200,
            'message' => []
        ], $response->jsonContent);
    }
}
