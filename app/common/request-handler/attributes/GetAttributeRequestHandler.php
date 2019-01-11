<?php
/**
 * User: Wajdi Jurry
 * Date: 27/12/18
 * Time: 06:01 م
 */

namespace Shop_categories\RequestHandler\Attributes;

use Phalcon\Validation;
use Phalcon\Validation\Message\Group;
use Shop_categories\Controllers\ControllerBase;
use Shop_categories\Exceptions\ArrayOfStringsException;
use Shop_categories\RequestHandler\RequestHandlerInterface;

class GetAttributeRequestHandler extends ControllerBase implements RequestHandlerInterface
{
    /** @var string $categoryId */
    private $categoryId;

    private $errorMessages;

    /**
     * @return string
     */
    public function getCategoryId()
    {
        return $this->categoryId;
    }

    /**
     * @param string $categoryId
     */
    public function setCategoryId(string $categoryId)
    {
        $this->categoryId = $categoryId;
    }

    /** Validate request fields using \Phalcon\Validation\Validator
     * @return Group
     */
    public function validate(): Group
    {
        $validator = new Validation();
        $validator->add(
            'categoryId',
            new Validation\Validator\Callback([
                'callback' => function($data) {
                    return !empty($data['categoryId']) &&$this->uuidUtil->isValid($data['categoryId']);
                },
                'message' => 'Invalid category id'
            ])
        );

        return $validator->validate(['categoryId' => $this->getCategoryId()]);
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        $messages = $this->validate();
        if (count($messages)) {
            foreach ($messages as $message) {
                $this->errorMessages[$message->getField()] = $message->getMessage();
            }
            return false;
        }
        return true;
    }

    public function notFound($message = 'Not Found')
    {
        // TODO: Implement notFound() method.
    }

    /**
     * @param null $message
     * @return void
     * @throws ArrayOfStringsException
     */
    public function invalidRequest($message = null)
    {
        if (is_null($message)) {
            $message = $this->errorMessages;
        }
        throw new ArrayOfStringsException($message, 400);
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

    public function toArray(): array
    {
        // TODO: Implement toArray() method.
    }
}