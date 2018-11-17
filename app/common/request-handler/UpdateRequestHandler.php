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

class UpdateRequestHandler extends ControllerBase implements RequestHandlerInterface
{
    /**
     * @var string $name
     */
    private $name;

    /**
     * @var string|null $parentId
     */
    private $parentId;

    /** @var int $order */
    private $order;


    public $validator;
    public $errorMessages = [];
    public $uuidUtil;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getParentId()
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
     * @param null|string $parentId
     */
    public function setParentId(?string $parentId): void
    {
        $this->parentId = $parentId;
    }

    public function setOrder(?int $order): void
    {
        $this->order = $order;
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
        $fields = [];
        $validator = new Validation();

        $validator->add(
            'parentId',
            new Validation\Validator\Callback([
                'callback' => function ($data) {
                    if (!is_null($data['parentId'])) {
                        return $this->getUuidUtil()->isValid($data['parentId']);
                    }
                    return true;
                },
                'message' => 'Invalid category parent Id'
            ])
        );

        $validator->add(
            'order',
            new Validation\Validator\Numericality([
                'allowEmpty' => true
            ])
        );

        if (isset($this->name)) {
            $validator->add(
                'name',
                new Validation\Validator\AlphaNumericValidator([
                    'whiteSpace' => true,
                    'underscore' => true,
                    "min" => 3,
                    'max' => 100,
                    'message' => 'Invalid category name',
                    'messageMinimum' => 'Category name should be at least 3 characters',
                    'messageMaximum' => 'Category name should not exceed 100 characters',
                    'allowEmpty' => false
                ])
            );
            $fields['name'] = $this->getName();
        }
        if (!is_null($this->parentId)) {
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
     * @param string $message
     * @throws \Exception
     */
    public function notFound($message = 'Not Found!')
    {
        http_response_code(404);
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

        http_response_code(400);
        throw new ArrayOfStringsException($message, 400);
    }

    /**
     * @param null $message
     */
    public function successRequest($message = null)
    {
        http_response_code(200);
        $this->response
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
        $result = [];

        if (!empty($this->getName())) {
            $result['categoryName'] = $this->getName();
        }

        if (array_key_exists('parentId', $this->request->getJsonRawBody(true))) {
            $result['categoryParentId'] = $this->getParentId();
        }

        if (!empty($this->getOrder())) {
            $result['categoryOrder'] = $this->getOrder();
        }

        return $result;
    }
}
