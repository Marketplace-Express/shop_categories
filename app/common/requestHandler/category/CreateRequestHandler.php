<?php
/**
 * User: Wajdi Jurry
 * Date: 28/07/18
 * Time: 01:47 م
 */

namespace app\common\requestHandler\category;

use app\common\requestHandler\RequestAbstract;
use app\common\services\user\UserService;
use app\common\validators\rules\CategoryRules;
use app\common\validators\UuidValidator;
use Phalcon\Mvc\Controller;
use Phalcon\Utils\Slug;
use Phalcon\Validation;
use Ramsey\Uuid\Uuid;

class CreateRequestHandler extends RequestAbstract
{
    /** @var string */
    private $name;

    /** @var int */
    private $order = 0;

    /** @var string */
    private $parentId;

    /** @var string */
    private $userId;

    /** @var string */
    private $vendorId;

    /** @var array */
    private $attributes;

    public function __construct(Controller $controller)
    {
        parent::__construct($controller, new CategoryRules());
        $this->setVendorId($this->getUserService()->vendorId);
        $this->setUserId($this->getUserService()->userId);
    }

    /**
     * @return UserService
     */
    private function getUserService()
    {
        return $this->controller->getDI()->get('userService');
    }

    /**
     * @param string
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @param string|null
     */
    public function setParentId($parentId)
    {
        $this->parentId = $parentId;
    }

    /** @param int */
    public function setOrder($order)
    {
        $this->order = $order;
    }

    /** @param string */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /** @param string */
    public function setVendorId($vendorId)
    {
        $this->vendorId = $vendorId;
    }

    /**
     * @param array|null $attributes
     */
    public function setAttributes(?array $attributes)
    {
        if ($attributes) {
            $attributes = array_map(function ($attribute) {
                return $this->controller->getJsonMapper()->map(
                    json_decode(json_encode($attribute)),
                    new \app\common\requestHandler\attribute\CreateRequestHandler($this->controller)
                );
            }, $attributes);
        }
        $this->attributes = $attributes;
    }

    /**
     * @return array
     */
    private function getAttributes()
    {
        return array_map(function ($attribute) {
            return $attribute->toArray();
        }, $this->attributes);
    }

    /**
     * @return Validation\Message\Group
     */
    public function validate() : Validation\Message\Group
    {
        $validator = new Validation();

        // Validate English input
        $validator->add(
            'name',
            new Validation\Validator\Callback([
                'callback' => function ($data) {
                    $name = preg_replace('/[\d\s_]/i', '', $data['name']); // clean string
                    if (preg_match('/[a-z]/i', $name) == false) {
                        return false;
                    }
                    return true;
                },
                'message' => 'English language only supported'
            ])
        );

        $validator->add(
            'name',
            new Validation\Validator\AlphaNumericValidator([
                'whiteSpace' => $this->validationRules->allowNameWhiteSpace,
                'underscore' => $this->validationRules->allowNameUnderscore,
                'min' => $this->validationRules->minNameLength,
                'max' => $this->validationRules->maxNameLength,
                'message' => 'Invalid category name',
                'messageMinimum' => 'Category name should be at least 3 characters',
                'messageMaximum' => 'Category name should not exceed 100 characters',
                'allowEmpty' => false
            ])
        );

        $validator->add(
            'parentId',
            new UuidValidator([
                'allowEmpty' => true
            ])
        );

        $validator->add(
            ['vendorId', 'userId'],
            new UuidValidator()
        );

        $validator->add(
            'order',
            new Validation\Validator\NumericValidator([
                'min' => $this->validationRules->minOrder,
                'max' => $this->validationRules->maxOrder,
                'message' => 'Category order should be a number',
                'allowEmpty' => true
            ])
        );

        $validator->add(
            'attributes',
            new Validation\Validator\Callback([
                'callback' => function ($data) {
                    if (!empty($data['attributes'])) {
                        return !in_array(false, array_map(function ($attribute) {
                            return $attribute->isValid();
                        }, $data['attributes']));
                    }
                    return true;
                },
                'message' => 'Invalid attributes'
            ])
        );

        // Fields to be validated
        $fields = [
            'name'      => $this->name,
            'parentId'  => $this->parentId,
            'order'     => $this->order,
            'userId'    => $this->userId,
            'vendorId'  => $this->vendorId,
            'attributes' => $this->attributes
        ];

        return $validator->validate($fields);
    }

    /**
     * @return array
     * @throws \Phalcon\Exception|
     * @throws \Exception
     */
    public function toArray(): array
    {
        return [
            'id' => Uuid::uuid4()->toString(),
            'parentId' => $this->parentId,
            'name' => $this->name,
            'order' => $this->order,
            'vendorId' => $this->vendorId,
            'userId' => $this->userId,
            'url' => (new Slug())->generate($this->name),
            'attributes' => $this->getAttributes()
        ];
    }
}
