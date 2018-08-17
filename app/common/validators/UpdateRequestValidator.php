<?php
/**
 * Created by PhpStorm.
 * User: wajdi
 * Date: 28/07/18
 * Time: 01:47 م
 */

namespace Shop_categories\Validators;

use Phalcon\Http\Response;
use Phalcon\Validation;
use Shop_categories\Modules\Api\Controllers\ControllerBase;

class UpdateRequestValidator extends ControllerBase implements RequestValidationInterface
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
     * @return Validation\Message\Group
     */
    public function validate() : Validation\Message\Group
    {
        $validator = new Validation();

        $validator->add('name',
            new Validation\Validator\AlphaNumericValidator([
                'whiteSpace' => true,
                'underscore' => true,
                'max' => 100,
                'message' => 'Invalid input',
                'allowEmpty' => true
            ])
        );

        $validator->add('parentId',
            new Validation\Validator\Callback([
                'callback' => function ($data) {
                    return \Ramsey\Uuid\Uuid::isValid($data['parentId']);
                },
                'message' => 'Invalid parent category Id',
                'allowEmpty' => true
            ]));

        if($this->getName()) {
            $fields['name'] = $this->getName();
        }

        if ($this->getParentId()) {
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

    /**
     * @param string $message
     * @return Response
     */
    public function notFound($message = 'Not Found!') : Response
    {
        return $this->response
            ->setStatusCode(404)
            ->setJsonContent([
                'status' => 404,
                'message' => $message
            ])->send();
    }

    public function invalidRequest($message = null) : Response
    {
        if (is_null($message)) {
            $message = $this->errorMessages;
        }
        return $this->response
            ->setStatusCode(400)
            ->setJsonContent([
                'status' => 400,
                'message' => $message
            ])
            ->send();
    }

    /**
     * @param null $message
     * @return Response
     */
    public function successRequest($message = null) : Response
    {
        return $this->response
            ->setStatusCode(200)
            ->setJsonContent([
                'status' => 200,
                'message' => $message
            ])
            ->send();
    }
}