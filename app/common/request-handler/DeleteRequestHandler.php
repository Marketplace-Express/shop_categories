<?php
/**

 * User: Wajdi Jurry
 * Date: 17/08/18
 * Time: 05:08 م
 */

namespace Shop_categories\RequestHandler;

use Phalcon\Validation\Message\Group;
use Shop_categories\Modules\Api\Controllers\ControllerBase;

class DeleteRequestHandler extends ControllerBase implements RequestHandlerInterface
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
        // response->setStatusCode slows down the performance
        // replacing it with http_response_code
        http_response_code(404);
        return $this->response
            ->setJsonContent([
                'status' => 404,
                'message' => $message
            ]);
    }

    public function invalidRequest($message = null)
    {
        // response->setStatusCode slows down the performance
        // replacing it with http_response_code
        http_response_code(400);
        return $this->response
            ->setJsonContent([
                'status' => 400,
                'message' => $message
            ]);
    }

    public function successRequest($message = null)
    {
        // response->setStatusCode slows down the performance
        // replacing it with http_response_code
        http_response_code(200);
        return $this->response
            ->setJsonContent([
                'status' => 200,
                'message' => $message
            ]);
    }

    public function toArray(): array
    {
        // TODO: Implement toArray() method.
    }
}