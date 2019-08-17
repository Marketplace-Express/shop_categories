<?php
/**
 * User: Wajdi Jurry
 * Date: 25/12/18
 * Time: 11:06 م
 */

namespace app\common\requestHandler\attribute;

use app\common\requestHandler\RequestAbstract;
use app\common\validators\rules\AttributeRules;
use app\common\validators\UuidValidator;
use Phalcon\Mvc\Controller;
use Phalcon\Validation;
use Phalcon\Validation\Message\Group;

/**
 * Class CreateRequestHandler
 * @package app\common\requestHandler\attribute
 */
class CreateRequestHandler extends RequestAbstract
{
    /** @var string $name */
    private $name;

    /** @var mixed $values */
    private $values;

    /**
     * CreateRequestHandler constructor.
     * @param Controller $controller
     */
    public function __construct(Controller $controller)
    {
        parent::__construct($controller, new AttributeRules());
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @param array $values
     */
    public function setValues($values)
    {
        $this->values = $values;
    }

    /** Validate request fields using \Phalcon\Validation\Validator
     * @return Group
     */
    public function validate(): Group
    {
        $validator = new Validation();
        $validator->add(
            'name',
            new Validation\Validator\AlphaNumericValidator([
                'whiteSpace' => $this->validationRules->allowAttrNameWhiteSpace,
                'underscore' => $this->validationRules->allowAttrNameUnderscore,
                'min' => $this->validationRules->minAttrNameLength,
                'max' => $this->validationRules->maxAttrNameLength,
                'message' => 'Invalid attribute name',
                'messageMinimum' => 'Attribute name should be at least 3 characters',
                'messageMaximum' => 'Attribute name should not exceed 50 characters',
                'allowEmpty' => false
            ])
        );

        $validator->add(
            'values',
            new Validation\Validator\Callback([
                'callback' => function ($data) {
                    return !empty($data['values']) && is_array($data['values'])
                        && array_unique($data['values']) === $data['values'];
                },
                'message' => 'Invalid input data or may contains duplicate values'
            ])
        );

        return $validator->validate([
            'name' => $this->name,
            'values' => $this->values
        ]);
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function toArray(): array
    {
        return [
            'attribute_name' => $this->name,
            'attribute_values' => $this->values
        ];
    }
}
