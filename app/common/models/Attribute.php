<?php
/**
 * User: Wajdi Jurry
 * Date: 25/12/18
 * Time: 10:32 م
 */

namespace Shop_categories\Models;

use Phalcon\Validation;
use Shop_categories\Utils\UuidUtil;

class Attribute extends BaseCollection
{
    /** @var string $attribute_category_id */
    public $attribute_category_id;

    /** @var string $attribute_name */
    public $attribute_name;

    /** @var string $attribute_key */
    public $attribute_key;

    /** @var array $attribute_values */
    public $attribute_values;

    /** @var bool $is_deleted */
    public $is_deleted = false;

    /**
     * @param array $attributes
     * @throws \Exception
     */
    public function setAttributes(array $attributes)
    {
        foreach ($attributes as $attribute => $value) {
            $this->$attribute = $value;
        }
    }

    /**
     * @param array|null $parameters
     * @return Attribute[]
     */
    public static function find(array $parameters = null)
    {
        $parameters[0]['is_deleted'] = false;
        return parent::find($parameters);
    }

    /**
     * @param array|null $parameters
     * @return Attribute|bool
     */
    public static function findFirst(array $parameters = null)
    {
        $parameters[0]['is_deleted'] = false;
        return parent::findFirst($parameters);
    }

    /**
     * @param mixed $id
     * @return Attribute|bool
     */
    public static function findById($id)
    {
        return parent::findById($id);
    }

    /**
     * Collection name
     * @return string
     */
    public function getSource()
    {
        return 'attributes';
    }

    /**
     * @throws \Exception
     */
    public function initialize()
    {
        $this->defaultBehavior();
    }

    public function beforeSave()
    {
        if ($this->_operationMade == self::OP_DELETE) {
            return;
        }
        $this->attribute_key = strtolower(str_replace(' ', '_', $this->attribute_name));
        $this->attribute_key = str_replace('(', '_', $this->attribute_key);
        $this->attribute_key = str_replace(')', '_', $this->attribute_key);
    }

    /**
     * @return bool|void
     * @throws \Exception
     */
    public function update()
    {
        throw new \Exception('Update not supported. Use save() instead', 500);
    }

    /**
     * @return array
     */
    public function toApiArray()
    {
        return [
            'attribute_id' => (string) $this->_id,
            'attribute_name' => $this->attribute_name,
            'attribute_key' => $this->attribute_key,
            'attribute_category_id' => $this->attribute_category_id,
            'attribute_values' => $this->attribute_values
        ];
    }

    /**
     * Validate model
     * @return bool
     */
    public function validation()
    {
        if ($this->_operationMade == self::OP_DELETE) {
            return true;
        }

        $validator = new Validation();

        $validator->add(
            'attribute_name',
            new Validation\Validator\AlphaNumericValidator([
                'whiteSpace' => $this->getDI()->getConfig()->application->attributeNameValidationConfig->allowWhiteSpace,
                'underscore' => $this->getDI()->getConfig()->application->attributeNameValidationConfig->allowUnderscore,
                'min' => $this->getDI()->getConfig()->application->attributeNameValidationConfig->minNameLength,
                'max' => $this->getDI()->getConfig()->application->attributeNameValidationConfig->maxNameLength,
                'message' => 'Invalid attribute name',
                'messageMinimum' => 'Attribute name should be at least 3 characters',
                'messageMaximum' => 'Attribute name should not exceed 50 characters',
                'allowEmpty' => true
            ])
        );

        $validator->add(
            'attribute_category_id',
            new Validation\Validator\Callback([
                'callback' => function ($data) {
                    return !empty($data['attribute_category_id']) && (new UuidUtil())->isValid($data['attribute_category_id']);
                },
                'message' => 'Invalid category id'
            ])
        );

        if ($this->_operationMade == self::OP_CREATE) {
            $validator->add(
                [
                    'attribute_name',
                    'attribute_category_id',
                    'is_deleted'
                ],
                new Validation\Validator\Uniqueness([
                    'convert' => function ($data) {
                        $data['is_deleted'] = false;
                        return $data;
                    },
                    'model' => Attribute::model(),
                    'message' => 'Category already contains this attribute'
                ])
            );
        }

        $validator->add(
            'attribute_values',
            new Validation\Validator\Callback([
                'callback' => function ($data) {
                    return !empty($data['attribute_values']) && is_array($data['attribute_values']);
                },
                'message' => 'Attribute values should be an array'
            ])
        );

        // TODO: move validation to product and remove from attribute
//        $validator->add(
//            'attribute_conditions',
//            new Validation\Validator\Callback([
//                'callback' => function ($data) {
//                    return !empty($data['attribute_conditions']) && is_array($data['attribute_conditions']);
//                },
//                'message' => 'Attribute conditions should be an array'
//            ])
//        );
//        $validator->add(
//            'conditions',
//            new Validation\Validator\Callback([
//                'callback' => function ($data) {
//                    $includeUsersIds = $data['conditions']['include']['usersIds'];
//                    $includeCountries = $data['conditions']['include']['countries'];
//                    $excludeUsersIds = $data['conditions']['exclude']['usersIds'];
//                    $excludeCountries = $data['conditions']['exclude']['countries'];
//                    if (!empty($includeUsersIds) && !empty($excludeUsersIds)
//                        && count(array_intersect($includeUsersIds, $excludeUsersIds))) {
//                        return false;
//                    }
//                    if (!empty($includeCountries) && !empty($excludeCountries)
//                        && count(array_intersect($includeCountries, $excludeCountries))) {
//                        return false;
//                    }
//                    return true;
//                },
//                'message' => 'Included values should not exist in excluded values'
//            ])
//        );

        $validator->add(
            'values',
            new Validation\Validator\Callback([
                'callback' => function($data) {
                    if (array_unique($data['values']) !== $data['values']) {
                        return false;
                    }
                    return true;
                },
                'message' => 'You must provide a unique values'
            ])
        );

        $this->_errorMessages = $messages =  $validator->validate([
            'attribute_name' => $this->attribute_name,
            'attribute_category_id' => $this->attribute_category_id,
            'attribute_conditions' => $this->attribute_conditions,
            'attribute_values' => $this->attribute_values
        ]);

        if (count($messages)) {
            return false;
        }
        return true;
    }
}