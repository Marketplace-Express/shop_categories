<?php
/**

 * User: Wajdi Jurry
 * Date: 17/08/18
 * Time: 04:46 م
 */

namespace Shop_categories\RequestHandler;


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
        http_response_code(404);
        return $this->response
            ->setJsonContent([
                'status' => 404,
                'message' => $message
            ]);
    }

    public function invalidRequest($message = null)
    {
        http_response_code(400);
        return $this->response
            ->setJsonContent([
                'status' => 400,
                'message' => $message
            ]);
    }

    public function successRequest($message = null)
    {
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