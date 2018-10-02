<?php
/**

 * User: Wajdi Jurry
 * Date: 24/07/18
 * Time: 11:51 م
 */

namespace Shop_categories\Controllers;

use Phalcon\Mvc\Controller;

/**
 * Class NotfoundController
 */

class NotfoundController extends Controller
{
    public function indexAction()
    {
        return $this->response
            ->setStatusCode(404)
            ->setJsonContent([
                'status' => 404,
                'message' => 'API not found'
            ]);
    }
}