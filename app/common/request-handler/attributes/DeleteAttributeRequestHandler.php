<?php
/**
 * User: Wajdi Jurry
 * Date: 29/12/18
 * Time: 10:46 م
 */

namespace Shop_categories\RequestHandler\Attributes;


use Phalcon\Validation\Message\Group;
use Shop_categories\Controllers\ControllerBase;
use Shop_categories\RequestHandler\RequestHandlerInterface;

class DeleteAttributeRequestHandler extends ControllerBase implements RequestHandlerInterface
{

    /** Validate request fields using \Phalcon\Validation\Validator
     * @return Group
     */
    public function validate(): Group
    {
        // TODO: Implement validate() method.
    }

    public function isValid(): bool
    {
        // TODO: Implement isValid() method.
    }

    public function notFound($message = 'Not Found')
    {
        // TODO: Implement notFound() method.
    }

    public function invalidRequest($message = null)
    {
        // TODO: Implement invalidRequest() method.
    }

    public function successRequest($message = null)
    {
        http_response_code(200);
        return $this->response
            ->setJsonContent([
                'status' => 200,
                'message' => 'Deleted'
            ]);
    }

    public function toArray(): array
    {
        // TODO: Implement toArray() method.
    }
}