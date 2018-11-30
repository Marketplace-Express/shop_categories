<?php
/**
 * User: Wajdi Jurry
 * Date: 28/07/18
 * Time: 01:47 م
 */

namespace Shop_categories\RequestHandler;

use Phalcon\Validation;
use Shop_categories\Exceptions\ArrayOfStringsException;
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

    /** @var int $order */
    private $order;

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

    public function getOrder(): ?int
    {
        return $this->order;
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

    public function setOrder(?int $order): void
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
                'messageMinimum' => 'Category name should be at least 3 characters',
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
            'order',
            new Validation\Validator\Numericality([
                'allowEmpty' => true
            ])
        );

        // Fields to be validated
        $fields['name'] = $this->getName();

        if ($this->getParentId()) {
            $fields['parentId'] = $this->getParentId();
        }

        if ($this->getOrder()) {
            $fields['order'] = $this->getOrder();
        }

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
     */
    public function toArray(): array
    {
        $result = [
            'categoryId' => $this->getCategoryId(),
            'categoryParentId' => $this->getParentId(),
            'categoryName' => $this->getName(),
            'categoryOrder' => $this->getOrder()
        ];

        return $result;
    }
}
