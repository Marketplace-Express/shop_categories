<?php
/**
 * User: Wajdi Jurry
 * Date: 31/12/18
 * Time: 08:12 م
 */

namespace app\common\requestHandler\attribute;


use app\common\requestHandler\RequestAbstract;
use app\common\validators\MongoIdValidator;
use app\common\validators\rules\AttributeRules;
use Phalcon\Mvc\Controller;
use Phalcon\Validation;
use Phalcon\Validation\Message\Group;

/**
 * Class UpdateRequestHandler
 * @package app\common\requestHandler\attribute
 */
class UpdateRequestHandler extends RequestAbstract
{
    /** @var string */
    private $id;

    /** @var string|null */
    private $name;

    /** @var array|null $values */
    private $values;

    /**
     * UpdateRequestHandler constructor.
     * @param Controller $controller
     */
    public function __construct(Controller $controller)
    {
        parent::__construct($controller, new AttributeRules());
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /** @param string|null $name */
    public function setName(?string $name)
    {
        $this->name = $name;
    }

    /**
     * @param array|null $values
     */
    public function setValues(?array $values)
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
            'id',
            new MongoIdValidator()
        );

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
                'allowEmpty' => true
            ])
        );

        $validator->add(
            'values',
            new Validation\Validator\Callback([
                'callback' => function ($data) {
                    if (!empty($data['values'])) {
                        return is_array($data['values'])
                            && array_unique($data['values']) === $data['values'];
                    }
                    return true;
                },
                'message' => 'Invalid input data or may contains duplicate values'
            ])
        );

        return $validator->validate([
            'id' => $this->id,
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
        $result['attribute_id'] = $this->id;

        if (!empty($this->name)) {
            $result['attribute_name'] = $this->name;
        }
        if (!empty($this->values)) {
            $result['attribute_values'] = $this->values;
        }
        return $result;
    }
}
