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

        // Fields to be validated
        $fields['name'] = $this->getName();

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

    public function notFound($message = 'Not Found')
    {
        http_response_code(404);
        return $this->response
            ->setJsonContent([
                'status' => 404,
                'message' => $message
            ]);
    }

    public function toArray(): array
    {
        $result = [
            'categoryId' => $this->getCategoryId(),
            'categoryParentId' => $this->getParentId(),
            'categoryName' => $this->getName()
        ];

        return $result;
    }
}
