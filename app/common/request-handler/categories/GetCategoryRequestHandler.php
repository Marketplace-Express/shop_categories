<?php
/**

 * User: Wajdi Jurry
 * Date: 17/08/18
 * Time: 04:46 م
 */

namespace Shop_categories\RequestHandler\Categories;

use Phalcon\Validation\Message\Group;
use Shop_categories\Controllers\ControllerBase;
use Shop_categories\RequestHandler\RequestHandlerInterface;

class GetCategoryRequestHandler extends ControllerBase implements RequestHandlerInterface
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


    /**
     * @param null $message
     */
    public function invalidRequest($message = null)
    {
        // TODO: Implement invalidRequest method.
    }

    /**
     * @param null $message
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface
     */
    public function successRequest($message = null)
    {
        http_response_code(200);
        return $this->response
            ->setJsonContent([
                'status' => 200,
                'message' => $message
            ]);
    }

    /**
     * @param string $message
     * @throws \Exception
     */
    public function notFound($message = 'Not Found')
    {
        // TODO: Implement notFound method.
    }

    public function toArray(): array
    {
        // TODO: Implement toArray() method.
    }
}