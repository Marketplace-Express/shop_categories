<?php
/**

 * User: Wajdi Jurry
 * Date: 17/08/18
 * Time: 04:46 م
 */

namespace Shop_categories\RequestHandler;


use Phalcon\Http\Response;
use Phalcon\Validation\Message\Group;
use Shop_categories\Modules\Api\Controllers\ControllerBase;

class GetRequestHandler extends ControllerBase implements RequestHandlerInterface
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

    public function notFound($message = 'Not Found')
    {
        $this->response->setStatusCode(404)
            ->setJsonContent([
                'status' => 404,
                'message' => $message
            ])->send();
        die();
    }

    public function invalidRequest($message = null)
    {
        $this->response->setStatusCode(400)
            ->setJsonContent([
                'status' => 400,
                'message' => $message
            ])->send();
        die();
    }

    public function successRequest($message = null)
    {
        $this->response->setStatusCode(200)
            ->setJsonContent([
                'status' => 200,
                'message' => $message
            ])->send();
        die();
    }

    public function toArray(): array
    {
        // TODO: Implement toArray() method.
    }
}