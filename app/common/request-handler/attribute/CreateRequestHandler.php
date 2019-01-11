<?php
/**
 * User: Wajdi Jurry
 * Date: 25/12/18
 * Time: 11:06 م
 */

namespace Shop_categories\RequestHandler\Attribute;

use Phalcon\Validation;
use Phalcon\Validation\Message\Group;
use Shop_categories\Controllers\ControllerBase;
use Shop_categories\Exceptions\ArrayOfStringsException;
use Shop_categories\RequestHandler\RequestHandlerInterface;

class CreateRequestHandler extends ControllerBase implements RequestHandlerInterface
{
    /** @var string $name */
    private $name;

    /** @var string $categoryId */
    private $categoryId;

    /** @var mixed $values */
    private $values;

    private $errorMessages;

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @param string $categoryId
     */
    public function setCategoryId(string $categoryId)
    {
        $this->categoryId = $categoryId;
    }

    /**
     * @param mixed $values
     */
    public function setValues($values)
    {
        $this->values = $values;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getCategoryId()
    {
        return $this->categoryId;
    }

    /**
     * @return mixed
     */
    public function getValues()
    {
        return $this->values;
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
                'allowEmpty' => false
            ])
        );

        $validator->add(
            'categoryId',
            new Validation\Validator\Callback([
                'callback' => function ($data) {
                    return !empty($data['categoryId']) && $this->uuidUtil->isValid($data['categoryId']);
                },
                'message' => 'Invalid category id'
            ])
        );

        // TODO: move validation to product and remove from attribute
//        $validator->add(
//            'conditions',
//            new Validation\Validator\Callback([
//                'callback' => function ($data) {
//                    return !empty($data['conditions']) && is_array($data['conditions']);
//                },
//                'message' => 'Attribute conditions should be an array'
//            ])
//        );
//        $validator->add(
//            'conditions',
//            new Validation\Validator\Callback([
//                'callback' => function ($data) {
//                    $includeUsersIds = $data['conditions']['include']['usersIds'];
//                    $includeCountries = $data['conditions']['include']['countries'];
//                    $excludeUsersIds = $data['conditions']['exclude']['usersIds'];
//                    $excludeCountries = $data['conditions']['exclude']['countries'];
//                    if (!empty($includeUsersIds) && !empty($excludeUsersIds)
//                        && count(array_intersect($includeUsersIds, $excludeUsersIds))) {
//                        return false;
//                    }
//                    if (!empty($includeCountries) && !empty($excludeCountries)
//                        && count(array_intersect($includeCountries, $excludeCountries))) {
//                        return false;
//                    }
//                    return true;
//                },
//                'message' => 'Included values should not exist in excluded values'
//            ])
//        );

        $validator->add(
            'values',
            new Validation\Validator\Callback([
                'callback' => function ($data) {
                    return !empty($data['values']) && is_array($data['values']);
                },
                'message' => 'Attribute values should be an array'
            ])
        );

        $validator->add(
            'values',
            new Validation\Validator\Callback([
                'callback' => function($data) {
                    if (array_unique($data['values']) !== $data['values']) {
                        return false;
                    }
                    return true;
                },
                'message' => 'You must provide a unique values'
            ])
        );

        return $validator->validate([
            'name' => $this->getName(),
            'categoryId' => $this->getCategoryId(),
            'conditions' => $this->getConditions(),
            'values' => $this->getValues()
        ]);
    }

    /**
     * Check if request is valid
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
        return [
            'attribute_name' => $this->getName(),
            'attribute_category_id' => $this->getCategoryId(),
            'attribute_conditions' => $this->getConditions(),
            'attribute_values' => $this->getValues()
        ];
    }
}