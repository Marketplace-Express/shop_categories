<?php
/**
 * User: Wajdi Jurry
 * Date: 28/07/18
 * Time: 01:47 م
 */

namespace app\common\requestHandler\category;

use app\common\requestHandler\RequestAbstract;
use app\common\validators\rules\CategoryRules;
use app\common\validators\UuidValidator;
use Phalcon\Utils\Slug;
use Phalcon\Validation;

class CreateRequestHandler extends RequestAbstract
{
    /** @var string */
    private $name;

    /** @var int */
    private $order = 0;

    /** @var string */
    private $parentId;

    /** @var array */
    private $attributes = [];

    /** @var string */
    private $storeId;

    /** @var string */
    private $userId;

    public function __construct()
    {
        parent::__construct(new CategoryRules());
    }

    /** @param array */
    public function setCategory($category)
    {
        $this->name = $category['name'];
        $this->parentId = $category['parentId'];
        $this->order = $category['order'];
        if ($category['attributes']) {
            $this->attributes = array_map(function ($attribute) {
                return $this->di->get('jsonMapper')->map(
                    (object)($attribute),
                    new \app\common\requestHandler\attribute\CreateRequestHandler()
                );
            }, $category['attributes']);
        }
    }

    /** @param string */
    public function setStoreId($storeId)
    {
        $this->storeId = $storeId;
    }

    /** @param string */
    public function setUserId($userId)
    {
        $this->userId = $userId;
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
            ['storeId', 'userId'],
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
            'name'       => $this->name,
            'parentId'   => $this->parentId,
            'order'      => $this->order,
            'attributes' => $this->attributes,
            'storeId'    => $this->storeId,
            'userId'     => $this->userId
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
            'id'         => $this->security->getRandom()->uuid(),
            'parentId'   => $this->parentId,
            'name'       => $this->name,
            'order'      => $this->order,
            'url'        => (new Slug())->generate($this->name),
            'attributes' => $this->getAttributes(),
            'storeId'    => $this->storeId,
            'userId'     => $this->userId
        ];
    }
}
