<?php
/**
 * User: Wajdi Jurry
 * Date: 28/07/18
 * Time: 01:47 م
 */

namespace Shop_categories\RequestHandler\Category;

use Phalcon\Validation;
use Shop_categories\Controllers\ControllerBase;
use Shop_categories\Exceptions\ArrayOfStringsException;
use Shop_categories\RequestHandler\RequestHandlerInterface;
use Shop_categories\Utils\UuidUtil;

class CreateRequestHandler extends ControllerBase implements RequestHandlerInterface
{
    /** @var string $categoryId */
    public $categoryId;

    /** @var string $name */
    private $name;

    /** @var int $order */
    private $order = 0;

    /** @var string $parentId */
    private $parentId;

    public $uuidUtil;
    public $validator;
    public $errorMessages = [];

    /**
     * @return string
     * @throws \Exception
     */
    public function getCategoryId()
    {
        return $this->getUuidUtil()->uuid();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string|null
     */
    public function getParentId()
    {
        return $this->parentId;
    }

    /**
     * @return int|null
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @param string $parentId
     */
    public function setParentId($parentId)
    {
        $this->parentId = $parentId;
    }

    public function setOrder($order)
    {
        $this->order = $order;
    }

    /**
     * @return UuidUtil
     */
    private function getUuidUtil(): UuidUtil
    {
        if (!$this->uuidUtil) {
            $this->uuidUtil = new UuidUtil();
        }
        return $this->uuidUtil;
    }

    private function getAppConfig()
    {
        return $this->getDI()->getConfig()->application;
    }

    /**
     * @return Validation\Message\Group
     */
    public function validate() : Validation\Message\Group
    {
        $validator = new Validation();

        $validator->add(
            'name',
            new Validation\Validator\AlphaNumericValidator([
                'whiteSpace' => $this->getAppConfig()->categoryNameValidationConfig->allowWhiteSpace,
                'underscore' => $this->getAppConfig()->categoryNameValidationConfig->allowUnderscore,
                'min' => $this->getAppConfig()->categoryNameValidationConfig->minNameLength,
                'max' => $this->getAppConfig()->categoryNameValidationConfig->maxNameLength,
                'message' => 'Invalid category name',
                'messageMinimum' => 'Category name should be at least 3 characters',
                'messageMaximum' => 'Category name should not exceed 100 characters',
                'allowEmpty' => false
            ])
        );

        $validator->add(
            'parentId',
            new Validation\Validator\Callback([
                'callback' => function ($data) {
                    if (!empty($data['parentId'])) {
                        return $this->getUuidUtil()->isValid($data['parentId']);
                    }
                    return true;
                },
                'message' => 'Invalid parent category Id'
            ])
        );

        $validator->add(
            'order',
            new Validation\Validator\NumericValidator([
                'min' => $this->getAppConfig()->categoryOrderValidationConfig->minCategoryOrder,
                'max' => $this->getAppConfig()->categoryOrderValidationConfig->maxCategoryOrder,
                'allowFloat' => $this->getAppConfig()->categoryOrderValidationConfig->allowFloat,
                'allowSign' => $this->getAppConfig()->categoryOrderValidationConfig->allowSign,
                'message' => 'Category order should be a number',
                'allowEmpty' => true
            ])
        );

        // Fields to be validated
        $fields = [
            'name'      => $this->getName(),
            'parentId'  => $this->getParentId(),
            'order'     => $this->getOrder()
        ];

        return $validator->validate($fields);
    }

    /**
     * @return bool
     */
    public function isValid() : bool
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
        throw new \Exception($message, 404);
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function toArray(): array
    {
        $result = [
            'categoryId' => $this->getCategoryId(),
            'categoryParentId' => $this->getParentId(),
            'categoryName' => $this->getName(),
            'categoryOrder' => $this->getOrder(),
            'categoryVendorId' => $this->request->getQuery('vendorId'),
            'categoryUserId' => 'fded67e4-9fcd-4a2d-ae2e-de15d70a8bb5'
        ];

        return $result;
    }
}
