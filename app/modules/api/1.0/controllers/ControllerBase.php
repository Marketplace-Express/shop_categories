<?php
/**
 * User: Wajdi Jurry
 * Date: 24/07/18
 * Time: 11:53 م
 */

namespace Shop_categories\Modules\Api\Controllers;

use Phalcon\Mvc\Controller;
use Shop_categories\Services\CategoryService;

class ControllerBase extends Controller
{
    /**
     * Columns to be returned
     */
    const PUBLIC_COLUMNS = ['categoryId', 'categoryName', 'categoryParentId'];

    /**
     * @var \JsonMapper $jsonMapper
     */
    public $jsonMapper;

    /**
     * @var CategoryService $service
     */
    public $service;

    /**
     * @return \JsonMapper
     */
    public function getJsonMapper(): \JsonMapper
    {
        if (!$this->jsonMapper) {
            $this->setJsonMapper(new \JsonMapper());
        }
        return $this->jsonMapper;
    }

    /**
     * @param \JsonMapper $jsonMapper
     */
    public function setJsonMapper(\JsonMapper $jsonMapper): void
    {
        $this->jsonMapper = $jsonMapper;
        $this->jsonMapper->bExceptionOnUndefinedProperty = true;
    }

    /**
     * @return CategoryService
     */
    public function getService(): CategoryService
    {
        if (!$this->service) {
            $this->service = new CategoryService();
        }
        return $this->service;
    }

    /**
     * @param CategoryService $service
     */
    public function setService(CategoryService $service): void
    {
        $this->service = $service;
    }

    /**
     * Forward client error response to ExceptionhandlerController
     * @param $errors
     * @param int $code
     */
    public function handleError($errors, $code = 500)
    {
        $this->dispatcher->forward([
            'controller' => 'exceptionHandler',
            'action' => 'raiseError',
            'params' => [$errors, $code]
        ]);
    }

    /**
     * Expose public columns from result set array
     * @param array $rows
     * @return array
     */
    public function showPublicColumns(array $rows)
    {
        $result = [];

        // Multi-dimensional array
        foreach ($rows as $key => $row) {
            if (is_array($row)) {
                foreach ($row as $index => $item) {
                    if (is_array($item)) {
                        $result[$key][$index] = $this->showPublicColumns($item);
                        continue;
                    }
                    $result[$key] = array_intersect_key($row, array_flip(self::PUBLIC_COLUMNS));
                }
            } elseif (empty($result) && !empty($rows)) {
                // One dimensional array
                $result = array_intersect_key($rows, array_flip(self::PUBLIC_COLUMNS));
            }
        }

        return $result;
    }
}
