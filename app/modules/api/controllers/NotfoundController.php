<?php
/**

 * User: Wajdi Jurry
 * Date: 24/07/18
 * Time: 11:51 م
 */

namespace app\modules\api\controllers;

use Phalcon\Mvc\Controller;

/**
 * Class NotfoundController
 */

class NotfoundController extends Controller
{
    public function indexAction()
    {
        http_response_code(404);
        return $this->response
            ->setJsonContent([
                'status' => 404,
                'message' => 'API not found'
            ]);
    }
}