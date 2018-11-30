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
    const PUBLIC_COLUMNS = ['categoryId', 'categoryName', 'categoryParentId', 'categoryOrder'];

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
     * @codeCoverageIgnore
     */
    public function getJsonMapper(): \JsonMapper
    {
        if (!$this->jsonMapper) {
            $this->jsonMapper = new \JsonMapper();
        }
        $this->jsonMapper->bExceptionOnUndefinedProperty = true;
        return $this->jsonMapper;
    }

    /**
     * @param \JsonMapper $jsonMapper
     * @codeCoverageIgnore
     */
    public function setJsonMapper(\JsonMapper $jsonMapper): void
    {
        $this->jsonMapper = $jsonMapper;
    }

    /**
     * @return CategoryService
     * @codeCoverageIgnore
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
     * @codeCoverageIgnore
     */
    public function setService(CategoryService $service): void
    {
        $this->service = $service;
    }

    /**
     * Forward error response to ExceptionhandlerController
     * @param $errors
     * @param int $code
     * @codeCoverageIgnore
     */
    public function handleError($errors, $code = 500)
    {
        $this->dispatcher->forward([
            'namespace' => 'Shop_categories\Controllers',
            'controller' => 'exceptionhandler',
            'action' => 'raiseError',
            'params' => [$errors, $code]
        ]);
    }

    /**
     * @param $message
     * @param int $code
     * @return void
     * @codeCoverageIgnore
     */
    public function sendResponse($message, int $code = 400)
    {
        // response->setStatusCode slows down the performance
        // replacing it with http_response_code
        http_response_code($code);
        $this->response
            ->setJsonContent([
                'status' => $code,
                'message' => $message
            ])->send();
        exit;
    }

    /**
     * Expose public columns from result set array
     * @param array $rows
     * @return array
     */
    public function showPublicColumns(array $rows)
    {
        $result = [];
        foreach ($rows as $key => $row) {
            if (is_array($row)) {
                // Multi-dimensional array
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
