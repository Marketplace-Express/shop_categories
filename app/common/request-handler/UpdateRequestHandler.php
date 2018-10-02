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

    public function notFound($message = 'Not Found!')
    {
        http_response_code(404);
        return $this->response
            ->setJsonContent([
                'status' => 404,
                'message' => $message
            ]);
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

    public function toArray(): array
    {
        $result = [];

        if (!empty($this->getName())) {
            $result['categoryName'] = $this->getName();
        }

        if (array_key_exists('parentId', $this->request->getJsonRawBody(true))) {
            $result['categoryParentId'] = $this->getParentId();
        }

        return $result;
    }
}
