<?php
/**
 * User: Wajdi Jurry
 * Date: ١٠‏/٨‏/٢٠١٩
 * Time: ١١:٥٤ م
 */

namespace app\common\models\mappers;


use app\common\exceptions\InvalidArgumentException;
use app\common\models\Category;
use app\common\utils\UuidUtil;
use app\common\validators\rules\CategoryRules;
use app\common\validators\UuidValidator;
use Phalcon\Validation;

class CategoryMapper
{
    private $model;
    private $data;
    private $allowedFields = [
        'id',
        'name',
        'order',
        'parentId',
        'vendorId',
        'userId'
    ];

    /** @var CategoryRules */
    private $validationRules;

    public function __construct(array $data)
    {
        $this->data = $data;
        $this->model = new Category();
        $this->validate();
    }

    /**
     * @return CategoryRules
     */
    private function getValidationRules(): CategoryRules
    {
        return $this->validationRules ?? $this->validationRules = new CategoryRules();
    }

    private function validate()
    {
        foreach ($this->data as $field => $value) {
            if (!in_array($field, $this->allowedFields)) {
                throw new InvalidArgumentException(
                    sprintf("Provided field %s is not supported", $field)
                );
            }
        }

        $validator = new Validation();
        $validator->add(
            'name',
            new Validation\Validator\AlphaNumericValidator([
                'whiteSpace' => $this->getValidationRules()->allowNameWhiteSpace,
                'underscore' => $this->getValidationRules()->allowNameUnderscore,
                'min' => $this->getValidationRules()->minNameLength,
                'max' => $this->getValidationRules()->maxNameLength,
                'message' => 'Invalid category name',
                'messageMinimum' => 'Category name should be at least 3 characters',
                'messageMaximum' => 'Category name should not exceed 100 characters',
                'allowEmpty' => false
            ])
        );

        $validator->add(
            ['parentId', 'userId', 'vendorId'],
            new UuidValidator()
        );

        $validator->add(
            'order',
            new Validation\Validator\NumericValidator([
                'min' => $this->getValidationRules()->minOrder,
                'max' => $this->getValidationRules()->maxOrder,
                'message' => 'Category order should be a number',
                'allowEmpty' => true
            ])
        );

        $messages = $validator->validate([
            'name' => $this->data['name'],
            'order' => $this->data['order'],
            'parentId' => $this->data['parentId'],
            'userId' => $this->data['userId'],
            'vendorId' => $this->data['vendorId']
        ]);

        if (count($messages)) {
            foreach ($messages as $message) {

            }
        }
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getId(): string
    {
        return (new UuidUtil())->uuid();
    }

    public function map()
    {
        return [
            'categoryId' => $this->getId(),
            'categoryName' => $this->data['name'],
            'categoryParentId' => $this->data['parentId'],
            'categoryOrder' => $this->data['order'],
            'categoryVendorId' => $this->data['vendorId'],
            'categoryUserId' => $this->data['userId']
        ];
    }
}
