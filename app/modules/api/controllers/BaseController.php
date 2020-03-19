<?php
/**
 * User: Wajdi Jurry
 * Date: 24/07/18
 * Time: 11:53 م
 */

namespace app\modules\api\controllers;

use Phalcon\Mvc\Controller;
use app\common\helpers\ArrayHelper;
use app\common\services\BaseService;
use app\common\utils\UuidUtil;

class BaseController extends Controller
{
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
            'namespace' => 'app\modules\api\controllers',
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
            ->setJsonContent($message)->send();
        exit;
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
     * @return mixed
     */
    protected function queryStringToObject(array $params)
    {
        unset($params['_url']);
        return json_decode(json_encode($params));
    }
}
