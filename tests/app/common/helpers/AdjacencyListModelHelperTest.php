<?php
/**
 * User: Wajdi Jurry
 * Date: 26/10/18
 * Time: 11:29 م
 */

namespace Shop_categories\Tests\Helpers;

use PHPUnit\Framework\MockObject\MockObject;
use Shop_categories\Helpers\AdjacencyListModelHelper;

class AdjacencyListModelHelperTest extends \UnitTestCase
{
    public $params;
    public function setUp()
    {
        parent::setUp();
        $this->params = [
            'itemIdAttribute' => 'itemId',
            'parentIdAttribute' => 'parentId',
            'subItemsSlug' => 'children',
            'noParentValue' => null
        ];
    }

    public function getAdjacencyListModelHelperMock($array, ...$methods)
    {
        return $this->getMockBuilder(AdjacencyListModelHelper::class)
            ->setConstructorArgs([$array, $this->params])
            ->setMethods($methods)
            ->getMock();
    }

    public function itemsSamples()
    {
        return [
            [
                [
                    // Actual
                    ['itemId' => 'item1', 'parentId' => null],
                    ['itemId' => 'item3', 'parentId' => 'item1'],
                    ['itemId' => 'item5', 'parentId' => 'item3'],
                    ['itemId' => 'item2', 'parentId' => null],
                    ['itemId' => 'item4', 'parentId' => 'item2']
                ],
                [
                    // Expected
                    [
                        'itemId' => 'item1',
                        'parentId' => null,
                        'children' => [
                            [
                                'itemId' => 'item3',
                                'parentId' => 'item1',
                                'children' => [
                                    [
                                        'itemId' => 'item5',
                                        'parentId' => 'item3'
                                    ]
                                ]
                            ]
                        ],
                    ],
                    [
                        'itemId' => 'item2',
                        'parentId' => null,
                        'children' => [
                            [
                                'itemId' => 'item4',
                                'parentId' => 'item2'
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * @param $actual
     * @param $expected
     *
     * @throws \Exception
     * @dataProvider itemsSamples
     */
    public function testPrepare($actual, $expected)
    {
        /** @var AdjacencyListModelHelper|MockObject $adjacencyListModelHelperMock */
        $adjacencyListModelHelperMock = $this->getAdjacencyListModelHelperMock($actual, 'nothing');

        $this->assertEquals($expected, $adjacencyListModelHelperMock->prepare());
    }
}
