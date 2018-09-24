<?php
/**
 * User: Wajdi Jurry
 * Date: 28/07/18
 * Time: 01:47 م
 */

namespace Shop_categories\RequestHandler;

use Phalcon\Validation;
use Shop_categories\Modules\Api\Controllers\ControllerBase;
use Shop_categories\Utils\UuidUtil;

class CreateRequestHandler extends ControllerBase implements RequestHandlerInterface
{
    /**
     * @var string $categoryId
     */
    public $categoryId;

    /**
     * @var string $name
     */
    private $name;

    /**
     * @var string $parentId
     */
    private $parentId;

    /**
     * @var string $vendorId
     * @required
     */
    private $vendorId;

    public $uuidUtil;
    public $validator;
    public $errorMessages = [];

    /**
     * @return string
     */
    public function getCategoryId(): string
    {
        if (!$this->categoryId) {
            $this->categoryId = $this->getUuidUtil()->uuid();
        }
        return $this->categoryId;
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
    public function getParentId() : ?string
    {
        return $this->parentId;
    }

    /**
     * @return string
     */
    public function getVendorId(): ?string
    {
        return $this->vendorId;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @param string $parentId
     */
    public function setParentId(string $parentId): void
    {
        $this->parentId = $parentId;
    }

    /**
     * @param string $vendorId
     */
    public function setVendorId(string $vendorId): void
    {
        $this->vendorId = $vendorId;
    }

    /**
     * @return mixed
     */
    private function getUuidUtil()
    {
        if (!$this->uuidUtil) {
            $this->uuidUtil = new UuidUtil();
        }
        return $this->uuidUtil;
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
                'whiteSpace' => true,
                'underscore' => true,
                'min' => 3,
                'max' => 100,
                'message' => 'Invalid category name',
                'messageMinimum' => 'Category name should be at least 5 characters',
                'messageMaximum' => 'Category name should not exceed 100 characters'
            ])
        );

        $validator->add(
            'parentId',
            new Validation\Validator\Callback([
                'callback' => function ($data) {
                    return $this->getUuidUtil()->isValid($data['parentId']);
                },
                'message' => 'Invalid parent category Id',
                'allowEmpty' => true
            ])
        );

        $validator->add(
            'vendorId',
            new Validation\Validator\Callback([
                'callback' => function ($data) {
                    return $this->getUuidUtil()->isValid($data['vendorId']);
                },
                'message' => 'Provide a valid vendor Id',
                'allowEmpty' => false
            ])
        );

        // Fields to be validated
        $fields['name'] = $this->getName();
        $fields['vendorId'] = $this->getVendorId();

        if ($this->getParentId()) {
            $fields['parentId'] = $this->getParentId();
        }

        return $validator->validate($fields);
    }

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

    public function invalidRequest($message = null)
    {
        if (is_null($message)) {
            $message = $this->errorMessages;
        }

        $this->response->setStatusCode(400)
            ->setJsonContent([
                'status' => 400,
                'message' => $message
            ])->send();
        exit;
    }

    public function successRequest($message = null)
    {
        $this->response->setStatusCode(200)
            ->setJsonContent([
                'status' => 200,
                'message' => $message
            ])->send();
        exit;
    }

    public function notFound($message = 'Not Found')
    {
        $this->response->setStatusCode(404)
            ->setJsonContent([
                'status' => 404,
                'message' => $message
            ])->send();
        exit;
    }

    public function toArray(): array
    {
        $result = [
            'categoryId' => $this->getCategoryId(),
            'categoryParentId' => $this->getParentId(),
            'categoryName' => $this->getName(),
            'vendorId' => $this->getVendorId(),
        ];

        return $result;
    }
}
