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
use Phalcon\Exception;
use Phalcon\Mvc\Controller;
use Phalcon\Utils\Slug;
use Phalcon\Validation;

/**
 * Class UpdateRequestHandler
 * @package app\common\requestHandler\category
 */
class UpdateRequestHandler extends RequestAbstract
{
    /** @var string */
    private $id;

    /** @var string */
    private $name;

    /** @var string|null */
    private $parentId;

    /** @var int|null */
    private $order;

    /**
     * @var \app\common\requestHandler\attribute\CreateRequestHandler[]|\app\common\requestHandler\attribute\UpdateRequestHandler[]
     */
    private $attributes;

    /**
     * UpdateRequestHandler constructor.
     * @param Controller $controller
     */
    public function __construct(Controller $controller)
    {
        parent::__construct($controller, new CategoryRules());
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @param null|string $parentId
     */
    public function setParentId($parentId)
    {
        $this->parentId = $parentId;
    }

    /**
     * @param int|null $order
     */
    public function setOrder($order)
    {
        $this->order = $order;
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
                    $this->getAttributesRequestHandler($attribute)
                );
            }, $attributes);
        }
        $this->attributes = $attributes;
    }

    /**
     * @param array $attribute
     * @return \app\common\requestHandler\attribute\CreateRequestHandler|\app\common\requestHandler\attribute\UpdateRequestHandler
     */
    private function getAttributesRequestHandler(array $attribute)
    {
        if (!empty($attribute['id'])) {
            return new \app\common\requestHandler\attribute\UpdateRequestHandler($this->controller);
        } else {
            return new \app\common\requestHandler\attribute\CreateRequestHandler($this->controller);
        }
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
        $validator->add(
            'parentId',
            new UuidValidator([
                'allowEmpty' => true
            ])
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
            'name',
            new Validation\Validator\AlphaNumericValidator([
                'whiteSpace' => $this->validationRules->allowNameWhiteSpace,
                'underscore' => $this->validationRules->allowNameUnderscore,
                'min' => $this->validationRules->minNameLength,
                'max' => $this->validationRules->maxNameLength,
                'message' => 'Invalid category name',
                'messageMinimum' => 'Category name should be at least 3 characters',
                'messageMaximum' => 'Category name should not exceed 100 characters',
                'allowEmpty' => true
            ])
        );

        // Validate English input
        $validator->add(
            'name',
            new Validation\Validator\Callback([
                'callback' => function ($data) {
                    $name = preg_replace('/[\d\s_]/i', '', $data['name']); // clean string
                    if (!empty($data['name']) && preg_match('/[a-z]/i', $name) == false) {
                        return false;
                    }
                    return true;
                },
                'message' => 'English language only supported'
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

        return $validator->validate([
            'name'      => $this->name,
            'parentId'  => $this->parentId,
            'order'     => $this->order,
            'attributes' => $this->attributes
        ]);
    }

    /**
     * @return array
     * @throws Exception
     */
    public function toArray(): array
    {
        $result = [
            'id' => $this->id
        ];

        if (!empty($this->name)) {
            $result['name'] = $this->name;
            $result['url'] = (new Slug())->generate($this->name);

        }

        if (!empty($this->parentId)) {
            $result['parentId'] = $this->parentId;
        }

        if (!empty($this->order) && $this->order !== null) {
            $result['order'] = $this->order;
        }

        if (!empty($this->attributes)) {
            $result['attributes'] = $this->getAttributes();
        }

        return $result;
    }
}
