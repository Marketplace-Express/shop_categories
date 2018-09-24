<?php
/**
 * User: Wajdi Jurry
 * Date: 20/08/18
 * Time: 05:19 م
 */

namespace Shop_categories\Tests\Modules\Api\Controllers;

use Shop_categories\Modules\Api\Controllers\IndexController;
use Shop_categories\Repositories\CategoryRepository;
use Shop_categories\Services\CategoryService;
use Shop_categories\RequestHandlers\GetRequestValidator;

class IndexControllerTest extends \UnitTestCase
{

    /**
     * @var IndexController $class
     */
    public $class;

    public $repoMock;

    const VENDOR_ID = '00492bc1-d22d-47b1-9372-8bc2ebf3c12d';

    public $categories;

    public function setUp()
    {
        parent::setUp();
        $this->class = new IndexController();
        $this->class->initialize();

        $this->categories = json_decode('[
            {
                "categoryId": "0b644e0a-754d-4b8c-b095-7a35a49abc16",
                "categoryName": "Sub parent",
                "categoryParentId": "0b644e0a-754d-4b8c-b095-7a35a49abc16"
            },
            {
                "categoryId": "44eec2a3-7ca5-4e27-ab0f-3294a4fdad45",
                "categoryName": "Parent Category",
                "categoryParentId": "44eec2a3-7ca5-4e27-ab0f-3294a4fdad45",
                "children": [
                    {
                        "categoryId": "cd6df4dc-a117-45c6-9a41-8a4293556042",
                        "categoryName": "Sub Category 1",
                        "categoryParentId": "44eec2a3-7ca5-4e27-ab0f-3294a4fdad45",
                        "children": [
                            {
                                "categoryId": "1d96fd93-dbfa-45cb-8a22-e05f47eca1ff",
                                "categoryName": "Sub Category 1_1",
                                "categoryParentId": "44eec2a3-7ca5-4e27-ab0f-3294a4fdad45"
                            }
                        ]
                    },
                    {
                        "categoryId": "4422b64e-b6e3-4d42-8cee-50c5b57e5f3d",
                        "categoryName": "Sub Category 2",
                        "categoryParentId": "44eec2a3-7ca5-4e27-ab0f-3294a4fdad45",
                        "children": [
                            {
                                "categoryId": "bae56c0e-ad41-4c95-97ed-bece419794b5",
                                "categoryName": "Sub Category 2_1",
                                "categoryParentId": "44eec2a3-7ca5-4e27-ab0f-3294a4fdad45"
                            },
                            {
                                "categoryId": "3b02a7d8-3776-46eb-9872-b10c01f43962",
                                "categoryName": "Sub Category 2_2",
                                "categoryParentId": "44eec2a3-7ca5-4e27-ab0f-3294a4fdad45",
                                "children": [
                                    {
                                        "categoryId": "d123c7c7-535e-445b-87a4-a53f675e1aa6",
                                        "categoryName": "Sub Category 2_2_1",
                                        "categoryParentId": "44eec2a3-7ca5-4e27-ab0f-3294a4fdad45"
                                    },
                                    {
                                        "categoryId": "60e46489-a4df-4461-b38b-9685f20db9c8",
                                        "categoryName": "Test 1",
                                        "categoryParentId": "44eec2a3-7ca5-4e27-ab0f-3294a4fdad45"
                                    },
                                    {
                                        "categoryId": "10d8ccf4-493e-4cdf-b2b9-fbcdded9220b",
                                        "categoryName": "Test 2",
                                        "categoryParentId": "44eec2a3-7ca5-4e27-ab0f-3294a4fdad45"
                                    },
                                    {
                                        "categoryId": "8de77fd2-ae32-432e-b752-2da2776b049a",
                                        "categoryName": "Test 3",
                                        "categoryParentId": "44eec2a3-7ca5-4e27-ab0f-3294a4fdad45",
                                        "children": [
                                            {
                                                "categoryId": "a11d8f14-0cda-487f-a9fe-015b141bceb7",
                                                "categoryName": "Sub Test 3",
                                                "categoryParentId": "44eec2a3-7ca5-4e27-ab0f-3294a4fdad45"
                                            }
                                        ]
                                    }
                                ]
                            }
                        ]
                    },
                    {
                        "categoryId": "86ec623d-47fe-43cc-bdb5-dd834d4bd0a2",
                        "categoryName": "Sub Category 3",
                        "categoryParentId": "44eec2a3-7ca5-4e27-ab0f-3294a4fdad45",
                        "children": [
                            {
                                "categoryId": "a82dc6ef-72f8-46d8-ac1e-738b7145b1ce",
                                "categoryName": "Sub Category 3_1",
                                "categoryParentId": "44eec2a3-7ca5-4e27-ab0f-3294a4fdad45",
                                "children": [
                                    {
                                        "categoryId": "592fbff7-33ea-41ce-b082-3862fe92ef49",
                                        "categoryName": "Sub Category 3_1_1",
                                        "categoryParentId": "44eec2a3-7ca5-4e27-ab0f-3294a4fdad45"
                                    }
                                ]
                            },
                            {
                                "categoryId": "6c9e263f-ef3f-4eb3-8d5b-c07bba4459a7",
                                "categoryName": "Sub Category 3_2",
                                "categoryParentId": "44eec2a3-7ca5-4e27-ab0f-3294a4fdad45",
                                "children": [
                                    {
                                        "categoryId": "ee68f439-b863-43a4-8df4-c347b575218a",
                                        "categoryName": "Sub Category 3_2_1",
                                        "categoryParentId": "44eec2a3-7ca5-4e27-ab0f-3294a4fdad45"
                                    }
                                ]
                            }
                        ]
                    },
                    {
                        "categoryId": "7b18a6c7-a4c9-41cc-aadc-e0abde15662d",
                        "categoryName": "Sub parent",
                        "categoryParentId": "44eec2a3-7ca5-4e27-ab0f-3294a4fdad45"
                    },
                    {
                        "categoryId": "a527966a-0f1d-41f1-a8e8-bd0552687f03",
                        "categoryName": "Sub parent",
                        "categoryParentId": "44eec2a3-7ca5-4e27-ab0f-3294a4fdad45",
                        "children": [
                            {
                                "categoryId": "2e3cc01a-5a1d-48ea-a450-6cceab691133",
                                "categoryName": "Test test",
                                "categoryParentId": "44eec2a3-7ca5-4e27-ab0f-3294a4fdad45"
                            }
                        ]
                    }
                ]
            }
        ]', true);
    }

    public function getServiceMock(string ...$methods)
    {
        return $this->getMockBuilder(CategoryService::class)
            ->setMethods($methods)
            ->getMock();
    }

    public function getRepoMock(string ...$methods)
    {
        return $this->getMockBuilder(CategoryRepository::class)
            ->setMethods($methods)
            ->getMock();
    }

    public function getJsonMapperMock(string ...$methods)
    {
        return$this->getMockBuilder(\JsonMapper::class)
            ->disableOriginalConstructor()
            ->setMethods($methods)
            ->getMock();
    }

    public function getGetRequestValidatorMock(string ...$methods)
    {
        return $this->getMockBuilder(GetRequestValidator::class)
            ->setMethods($methods)
            ->getMock();
    }
}
