<?php
/**
 * User: Wajdi Jurry
 * Date: 26/10/18
 * Time: 11:25 م
 */

namespace tests\app\common\controllers;

use app\modules\api\controllers\NotfoundController;
use tests\mocks\ResponseMock;
use tests\UnitTestCase;

class NotfoundControllerTest extends UnitTestCase
{
    /** @var NotfoundController */
    private $controller;

    public function  setUp()
    {
        $this->controller = new NotfoundController();
        parent::setUp();
    }

    public function testIndexAction()
    {
        /** @var ResponseMock $response */
        $response = $this->di->get('response');

        $this->controller->indexAction();

        $this->assertEquals(['status' => 404, 'message' => 'API not found'], $response->jsonContent);
    }
}
