<?php
/**
 * User: Wajdi Jurry
 * Date: 27/12/18
 * Time: 02:17 م
 */

namespace Shop_categories\RequestHandler\Attributes;

use Phalcon\Validation;
use Phalcon\Validation\Message\Group;
use Shop_categories\Controllers\ControllerBase;
use Shop_categories\Exceptions\ArrayOfStringsException;
use Shop_categories\RequestHandler\RequestHandlerInterface;

class UpdateAttributeRequestHandler extends ControllerBase implements RequestHandlerInterface
{
    /** @var string $name */
    private $name;

    /** @var string $categoryId */
    private $categoryId;

    private $errorMessages;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

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

    private function getAppConfig()
    {
        return $this->getDI()->getConfig()->application->attributeNameValidationConfig;
    }

    /** Validate request fields using \Phalcon\Validation\Validator
     * @return Group
     */
    public function validate(): Group
    {
        $validator = new Validation();
        $validator->add(
            'name',
            new Validation\Validator\AlphaNumericValidator([
                'whiteSpace' => $this->getAppConfig()->allowWhiteSpace,
                'underscore' => $this->getAppConfig()->allowUnderscore,
                'min' => $this->getAppConfig()->minNameLength,
                'max' => $this->getAppConfig()->maxNameLength,
                'message' => 'Invalid attribute name',
                'messageMinimum' => 'Attribute name should be at least 3 characters',
                'messageMaximum' => 'Attribute name should not exceed 50 characters',
                'allowEmpty' => true
            ])
        );

        $validator->add(
            'categoryId',
            new Validation\Validator\Callback([
                'callback' => function ($data) {
                    return empty($data['categoryId']) || $this->uuidUtil->isValid($data['categoryId']);
                },
                'message' => 'Invalid category id'
            ])
        );

        return $validator->validate([
            'name' => $this->getName(),
            'categoryId' => $this->getCategoryId()
        ]);
    }

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
        if (is_null($message)) {
            $message = $this->errorMessages;
        }
        throw new ArrayOfStringsException($message, 400);
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

    /**
     * @return array
     * @throws \Exception
     */
    public function toArray(): array
    {
        $result = [];
        if (!empty($this->getName())) {
            $result['attribute_name'] = $this->getName();
        }

        if (!empty($this->getCategoryId())) {
            $result['attribute_category_id'] = $this->getCategoryId();
        }

        if (empty($result)) {
            throw new \Exception('Nothing to update', 400);
        }

        return $result;
    }
}