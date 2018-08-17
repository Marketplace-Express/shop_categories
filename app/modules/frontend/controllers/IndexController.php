<?php

namespace Shop_categories\Modules\Frontend\Controllers;

/**
 * Class IndexController
 * @package Shop_categories\Modules\Frontend\Controllers
 * @RoutePrefix("/ui")
 */
class IndexController extends ControllerBase
{

    /**
     * @Get('/')
     */
    public function indexAction()
    {
        exit('asd');
    }

    /**
     * @Post('/edit')
     */
    public function editAction()
    {
        exit('edit');
    }
}

