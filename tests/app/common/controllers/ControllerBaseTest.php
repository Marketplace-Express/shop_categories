<?php
/**
 * User: Wajdi Jurry
 * Date: 17/08/18
 * Time: 10:36 م
 */

namespace Shop_categories\Tests\Controllers;

use Shop_categories\Controllers\BaseController;
use PHPUnit\Framework\MockObject\MockObject;

class ControllerBaseTest extends \UnitTestCase
{

    public function setUp()
    {
        parent::setUp();
    }

    public function getControllerMock(...$methods)
    {
        return $this->getMockBuilder(BaseController::class)
            ->setMethods($methods)
            ->getMock();
    }

    public function showPublicColumnsData()
    {
        return [
            [
                ['invalidColumn' => 'value1', 'anotherInvalidColumn' => 'value2', 'notPublicColumn' => 'value3', 'categoryOrder' => 3],
                ['categoryOrder' => 3]
            ],
            [
                ['xColumn' => 'value1', 'categoryName' => 'value2', 'notValidColumn' => 'value3'],
                ['categoryName' => 'value2']
            ],
            [
                ['categoryName' => 'value1', 'categoryParentId' => 'value2', 'categoryId' => 'value3'],
                ['categoryName' => 'value1', 'categoryParentId' => 'value2', 'categoryId' => 'value3']
            ],
            [
                ['categoryName' => 'value1', 'categoryParentId' => 'value2', 'doNotShow' => 'value3'],
                ['categoryName' => 'value1', 'categoryParentId' => 'value2']
            ],
            [
                ['notPublicColumn' => 'value1', 'tokenSecret' => 'value2', 'categoryId' => 'value3', 'order' => 22],
                ['categoryId' => 'value3']
            ],
            [
                [
                    'children' => [
                        'column1' => 'value1',
                        'column2' => 'value2',
                        'categoryId' => '1234'
                    ]
                ],
                [
                    'children' => [
                        'categoryId' => '1234'
                    ]
                ]
            ],
            [
                [
                    'children' => [
                        'categoryId' => '1234',
                        'children' => [
                            'categoryName' => 'my category'
                        ]
                    ]
                ],
                [
                    'children' => [
                        'categoryId' => '1234',
                        'children' => [
                            'categoryName' => 'my category'
                        ]
                    ]
                ]
            ],
            [
                [
                    'category1' => [
                        'categoryId' => 'category1',
                        'children' => null
                    ]
                ],
                [
                    'category1' => [
                        'categoryId' => 'category1'
                    ]
                ]
            ]
        ];
    }

    /**
     * @dataProvider showPublicColumnsData
     * @param $expected
     * @param $actual
     */
    public function testShowPublicColumns($actual, $expected)
    {
        /** @var BaseController|MockObject $controllerMock */
        $controllerMock = $this->getControllerMock('nothing');

        $this->assertEquals($expected, $controllerMock->showPublicColumns($actual));
    }
}