<?php
/**
 * Created by PhpStorm.
 * User: wajdi
 * Date: 29/07/18
 * Time: 10:41 م
 */

namespace Shop_categories\Modules\Api\Controllers;

class ExceptionHandlerController extends ControllerBase
{
    /**
     * @param $messages
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface
     */
    public function serverErrorAction($messages)
    {
        return $this->response
            ->setStatusCode(500)
            ->setJsonContent([
                'status' => 500,
                'message' => $messages
            ]);
    }

    /**
     * @param int $status
     * @param mixed $messages
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface
     */
    public function badRequestAction(int $status = 400, $messages = 'Bad Request')
    {
        return $this->response
            ->setStatusCode($status)
            ->setJsonContent([
                'status' => $status,
                'message' => $messages
            ])
            ->send();
    }
}