<?php
/**
 * User: Wajdi Jurry
 * Date: 27/02/19
 * Time: 09:24 م
 */

namespace Shop_categories\RequestHandler\Category;


use Phalcon\Validation;
use Phalcon\Validation\Message\Group;
use Shop_categories\Controllers\BaseController;
use Shop_categories\Exceptions\ArrayOfStringsException;
use Shop_categories\RequestHandler\RequestHandlerInterface;

class SearchRequestHandler extends BaseController implements RequestHandlerInterface
{

    /** @var string */
    private $keyword;

    private $errorMessages;

    /**
     * @return string
     */
    public function getKeyword()
    {
        return $this->keyword;
    }

    /**
     * @param string $keyword
     */
    public function setKeyword(string $keyword): void
    {
        $this->keyword = $keyword;
    }

    /** Validate request fields using \Phalcon\Validation\Validator
     * @return Group
     */
    public function validate(): Group
    {
        $validator = new Validation();
        $validator->add(
            'keyword',
            new Validation\Validator\PresenceOf()
        );
        return $validator->validate([
            'keyword' => $this->getKeyword()
        ]);
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
     * @return mixed|void
     * @throws ArrayOfStringsException
     */
    public function invalidRequest($message = null)
    {
        http_response_code(400);
        throw new ArrayOfStringsException($this->errorMessages);
    }

    /**
     * @param mixed $message
     * @return mixed|\Phalcon\Http\Response|\Phalcon\Http\ResponseInterface
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
     * @return array
     */
    public function toArray(): array
    {
        return [
            'keyword' => $this->getKeyword()
        ];
    }
}