<?php
/**
 * User: Wajdi Jurry
 * Date: 24/07/18
 * Time: 11:53 م
 */

namespace Shop_categories\Controllers;

use Phalcon\Mvc\Controller;
use Shop_categories\Helpers\ArrayHelper;
use Shop_categories\Services\BaseService;
use Shop_categories\Services\CategoryService;
use Shop_categories\Utils\UuidUtil;

class ControllerBase extends Controller
{
    /**
     * Columns to be returned
     * @deprecated
     */
    const PUBLIC_COLUMNS = ['categoryId', 'categoryName', 'categoryParentId', 'categoryOrder'];

    /**
     * @var \JsonMapper $jsonMapper
     */
    public $jsonMapper;

    /**
     * @var $service
     */
    public $service;

    /**
     * @var UuidUtil $uuidUtil
     */
    public $uuidUtil;

    /**
     * @param BaseService $service
     * @codeCoverageIgnore
     */
    public function setService(BaseService $service): void
    {
        $this->service = $service;
    }

    /**
     * @param mixed $uuidUtil
     */
    public function setUuidUtil($uuidUtil): void
    {
        $this->uuidUtil = $uuidUtil;
    }

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
     * Initialize controller
     */
    public function onConstruct()
    {
        $this->setUuidUtil(new UuidUtil());
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
     * @deprecated
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

    /**
     * Represent items as tree
     * @param array $data
     * @return array
     * @throws \Exception
     */
    public function toTree(array $data)
    {
        return (new ArrayHelper($data, [
            'itemIdAttribute' => 'categoryId',
            'parentIdAttribute' => 'categoryParentId'
        ]))->tree();
    }

    /**
     * @param array $params
     * @return \stdClass
     */
    protected function queryStringToObject(array $params)
    {
        $object = new \stdClass();
        unset($params['_url']);
        foreach ($params as $key => $value) {
            $object->$key = $value;
        }
        return $object;
    }
}
