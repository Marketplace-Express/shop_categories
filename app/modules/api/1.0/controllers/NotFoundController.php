<?php
/**
 * Created by PhpStorm.
 * User: wajdi
 * Date: 24/07/18
 * Time: 11:51 م
 */

namespace Shop_categories\Modules\Api\Controllers;

/**
 * Class NotFoundController
 * @package Shop_categories\Modules\Api\Controllers
 * @RoutePrefix('/api/1.0')
 */

class NotFoundController extends ControllerBase
{
    public function indexAction()
    {
        echo 'API: not found';
    }
}