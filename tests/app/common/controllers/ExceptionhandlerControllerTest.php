<?php
/**
 * User: Wajdi Jurry
 * Date: 26/10/18
 * Time: 06:23 م
 */

namespace Shop_categories\Tests\Controllers;

use Phalcon\Logger\Adapter\File;
use PHPUnit\Framework\MockObject\MockObject;
use Shop_categories\Controllers\ExceptionhandlerController;
use Shop_categories\Tests\Mocks\ResponseMock;

class ExceptionhandlerControllerTest extends \UnitTestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    public function getControllerMock(...$methods)
    {
        return $this->getMockBuilder(ExceptionhandlerController::class)
            ->setMethods($methods)
            ->getMock();
    }

    public function getFileObjectMock(...$methods)
    {
        return $this->getMockBuilder(File::class)
            ->setMethods($methods)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function responseSamples()
    {
        return [
            [
                [
                    'status' => 400,
                    'message' => ['error1', 'error2']
                ]
            ],
            [
                [
                    'status' => 500,
                    'message' => 'sample error'
                ]
            ],
            [
                [
                    'status' => 404,
                    'message' => 'not found error'
                ]
            ]
        ];
    }

    /**
     * @param $expectedResponse
     *
     * @dataProvider responseSamples
     */
    public function testRaiseErrorAction($expectedResponse)
    {
        /** @var ResponseMock $response */
        $response = $this->di->get('response');

        /** @var ExceptionhandlerController|MockObject $controllerMock */
        $controllerMock = $this->getControllerMock('logError');
        $controllerMock->expects(self::once())->method('logError')->with($expectedResponse['message']);
        $controllerMock->raiseErrorAction($expectedResponse['message'], $expectedResponse['status']);

        $this->assertEquals($response->jsonContent, $expectedResponse);
    }

    public function errorsSamples()
    {
        return [
            [
                implode( '\n', ['error1', 'error2'])
            ],
            [
                'error1'
            ]
        ];
    }

    /**
     * @param $errors
     *
     * @dataProvider errorsSamples
     */
    public function testLogError($errors)
    {
        /** @var ExceptionhandlerController|MockObject $controllerMock */
        $controllerMock = $this->getControllerMock('nothing');

        /** @var File|MockObject $fileObjectMock */
        $fileObjectMock = $this->getFileObjectMock('log');
        $fileObjectMock->expects(self::once())->method('log')->with(\Phalcon\Logger::ERROR, $errors);
        $controllerMock->setFile($fileObjectMock);

        $controllerMock->logError($errors);

    }
}
