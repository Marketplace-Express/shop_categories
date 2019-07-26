<?php
/**

 * User: Wajdi Jurry
 * Date: 17/08/18
 * Time: 05:08 م
 */

namespace Shop_categories\RequestHandler\Category;

use Phalcon\Validation\Message\Group;
use Shop_categories\Controllers\BaseController;
use Shop_categories\Exceptions\ArrayOfStringsException;
use Shop_categories\RequestHandler\RequestHandlerInterface;

class DeleteRequestHandler extends BaseController implements RequestHandlerInterface
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
     * @param string $message
     * @throws \Exception
     */
    public function notFound($message = 'Not Found')
    {
        throw new \Exception($message, 404);
    }

    /**
     * @param null $message
     * @throws ArrayOfStringsException
     */
    public function invalidRequest($message = null)
    {
        throw new ArrayOfStringsException($message, 400);
    }

    /**
     * @param null $message
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface
     */
    public function successRequest($message = null)
    {
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