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
        $validator = new Validation();

        if (isset($this->name)) {
            $validator->add(
                'name',
                new Validation\Validator\AlphaNumericValidator([
                    'whiteSpace' => true,
                    'underscore' => true,
                    'max' => 100,
                    'message' => 'Invalid input',
                    'messageMinimum' => 'Category name should be at least 5 characters',
                    'messageMaximum' => 'Category name should not exceed 100 characters'
                ])
            );
            $fields['name'] = $this->getName();
        }

        if (!empty($this->parentId)) {
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
            $fields['parentId'] = $this->getParentId();
        }

        return $validator->validate($fields ?? []);
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
        $this->response->setStatusCode(404)
            ->setJsonContent([
                'status' => 404,
                'message' => $message
            ])->send();
        exit;
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

    public function toArray(): array
    {
        $result = [];

        if (!empty($this->getName())) {
            $result['categoryName'] = $this->getName();
        }

        if (isset($this->parentId)) {
            $result['categoryParentId'] = $this->getParentId();
        }

        return $result;
    }
}
