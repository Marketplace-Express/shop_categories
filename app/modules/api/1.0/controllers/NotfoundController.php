<?php
/**

 * User: Wajdi Jurry
 * Date: 24/07/18
 * Time: 11:51 م
 */

namespace Shop_categories\Modules\Api\Controllers;

/**
 * Class NotfoundController
 * @package Shop_categories\Modules\Api\Controllers
 * @RoutePrefix('/api/1.0')
 */

class NotfoundController extends ControllerBase
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