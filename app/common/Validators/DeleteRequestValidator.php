<?php
/**
 * Created by PhpStorm.
 * User: wajdi
 * Date: 17/08/18
 * Time: 05:08 م
 */

namespace Shop_categories\Validators;


use Phalcon\Http\Response;
use Phalcon\Validation\Message\Group;
use Shop_categories\Modules\Api\Controllers\ControllerBase;

class DeleteRequestValidator extends ControllerBase implements RequestValidationInterface
{

    /** Validate request fields using \Phalcon\Validation\Validator
     * @return Group
     */
    public function validate(): Group
    {
        return new Group();
    }

    public function isValid(): bool
    {
        return true;
    }

    public function notFound($message = 'Not Found'): Response
    {
        return $this->response->setStatusCode(404)
            ->setJsonContent([
                'status' => 404,
                'message' => $message
            ])->send();
    }

    public function invalidRequest($message = null): Response
    {
        return $this->response->setStatusCode(400)
            ->setJsonContent([
                'status' => 400,
                'message' => $message
            ])->send();
    }

    public function successRequest($message = null): Response
    {
        return $this->response->setStatusCode(200)
            ->setJsonContent([
                'status' => 200,
                'message' => $message
            ])->send();
    }
}