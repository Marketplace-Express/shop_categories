<?php
/**
 * Created by PhpStorm.
 * User: wajdi
 * Date: 24/07/18
 * Time: 11:53 م
 */

namespace Shop_categories\Modules\Api\Controllers;


use Phalcon\Mvc\Controller;

class ControllerBase extends Controller
{
    /**
     * Columns to be returned
     */
    const PUBLIC_COLUMNS = ['categoryId', 'categoryName', 'categoryParentId', 'children'];

    /**
     * @var \JsonMapper $jsonMapper
     */
    public $jsonMapper;

    public $exceptionHandler;

    public function initialize()
    {
        // Initialize Json Mapper
        $this->jsonMapper = new \JsonMapper();
        $this->jsonMapper->bExceptionOnUndefinedProperty = true;

        // Initialize ExceptionHandlerController
        $this->exceptionHandler = new ExceptionHandlerController();
    }

    /**
     * Forward server error response to ExceptionHandlerController
     * @param mixed $errors
     */
    public function handleServerError($errors)
    {
        return $this->dispatcher->forward([
            'controller' => 'exceptionHandler',
            'action' => 'serverError',
            'params' => [$errors]
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
                    } else {
                        $result[$key] = array_intersect_key($row, array_flip(self::PUBLIC_COLUMNS));
                    }
                }
            }
        }

        // One-dimension array
        if (empty($result)) {
            $result[] = array_intersect_key($rows, array_flip(self::PUBLIC_COLUMNS));
        }

        return $result;
    }
}