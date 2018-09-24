<?php
/**
 * User: Wajdi Jurry
 * Date: 17/08/18
 * Time: 10:36 م
 */

namespace Shop_categories\Tests\Modules\Api\Controllers;


use Shop_categories\Modules\Api\Controllers\ControllerBase;

class ControllerBaseTest extends \UnitTestCase
{
    /**
     * @var ControllerBase $class
     */
    public $class;

    public function setUp()
    {
        parent::setUp();
        $this->class = new ControllerBase();
    }

    public function showPublicColumnsData()
    {
        $this->setUp();
        return [
            [
                ['invalidColumn' => 'value1', 'anotherInvalidColumn' => 'value2', 'notPublicColumn' => 'value3'],
                []
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
                ['notPublicColumn' => 'value1', 'tokenSecret' => 'value2', 'categoryId' => 'value3'],
                ['categoryId' => 'value3']
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
        $this->assertEquals($expected, $this->class->showPublicColumns($actual));
    }
}