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
use app\common\exceptions\ArrayOfStringsException;
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

    public function __construct(Controller $controller)
    {
        parent::__construct($controller, new CategoryRules());
        $this->setVendorId($controller->request->getQuery('vendorId'));
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

    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    public function setVendorId($vendorId)
    {
        $this->vendorId = $vendorId;
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

        // Fields to be validated
        $fields = [
            'name'      => $this->name,
            'parentId'  => $this->parentId,
            'order'     => $this->order,
            'userId'    => $this->userId,
            'vendorId'  => $this->vendorId
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
            'categoryId' => Uuid::uuid4()->toString(),
            'categoryParentId' => $this->parentId,
            'categoryName' => $this->name,
            'categoryOrder' => $this->order,
            'categoryVendorId' => $this->vendorId,
            'categoryUserId' => $this->userId,
            'categoryUrl' => (new Slug())->generate($this->name)
        ];
    }
}
