<?php

namespace app\common\models;

use app\common\validators\rules\CategoryRules;
use Phalcon\Config;
use Phalcon\Validation;
use Phalcon\Validation\Validator\AlphaNumericValidator;
use app\common\models\behaviors\AdjacencyListModelBehaviorInterface;
use app\common\utils\UuidUtil;
use app\common\validators\ExistenceValidator;

/**
 * Category
 * 
 * @package app\common\models
 * @autogenerated by Phalcon Developer Tools
 * @date 2018-09-13, 19:46:12
 */
class Category extends Base
{
    const WHITE_LIST = [
        'categoryId',
        'categoryParentId',
        'categoryVendorId',
        'categoryUserId',
        'categoryName',
        'categoryOrder',
        'categoryUrl',
        'categoryDepth'
    ];

    /**
     * @var string
     * @Primary
     * @Column(column="category_id", type="string", length=36, nullable=false)
     */
    public $categoryId;

    /**
     * @var string
     * @Column(column="category_parent_id", type="string", length=36)
     */
    public $categoryParentId;

    /**
     * @var string
     * @Column(column="category_vendor_id", type="string", length=36, nullable=false)
     */
    public $categoryVendorId;

    /**
     * @var string $categoryUserId
     * @Column(column="category_user_id", type="string", length=36, nullable=false)
     */
    public $categoryUserId;

    /**
     * @var integer $categoryOrder
     * @Column(column="category_order", type="integer", length=3, nullable=false)
     */
    public $categoryOrder = 0;

    /**
     * @var string
     * @Column(column="category_name", type="string", length=100, nullable=false)
     */
    public $categoryName;

    /**
     * @var string
     * @Column(column="category_url", type="string", length=255)
     */
    public $categoryUrl;

    /**
     * @var int
     * @Column(column="category_depth", type="integer", size=2, nullable=false)
     */
    public $categoryDepth = 0;

    /**
     * @var string
     * @Column(column="created_at", type="string", nullable=false)
     */
    public $createdAt;

    /**
     * @var string
     * @Column(column="updated_at", type="string")
     */
    public $updatedAt;

    /**
     * @var string
     * @Column(column="deleted_at", type="string")
     */
    public $deletedAt;

    /**
     * @var integer
     * @Column(column="is_deleted", type="integer", length=1, nullable=false)
     */
    public $isDeleted = false;

    /**
     * @throws \Exception
     */
    public function initialize()
    {
        $this->setSource("category");
        $this->useDynamicUpdate(true);

        $this->defaultBehavior();

        $this->addBehavior(new AdjacencyListModelBehaviorInterface([
            'itemIdAttribute' => 'categoryId',
            'parentIdAttribute' => 'categoryParentId',
            'orderByAttribute' => 'categoryOrder',
            'isDeletedAttribute' => 'isDeleted',
            'isDeletedValue' => false,
            'subItemsSlug' => 'children',
            'noParentValue' => null
        ]));

        $this->skipAttributesOnUpdate(['categoryVendorId']);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'category';
    }

    /**
     * Independent Column Mapping.
     * Keys are the real names in the table and the values their names in the application
     *
     * @return array
     */
    public function columnMap()
    {
        return [
            'category_id' => 'categoryId',
            'category_parent_id' => 'categoryParentId',
            'category_name' => 'categoryName',
            'category_order' => 'categoryOrder',
            'category_vendor_id' => 'categoryVendorId',
            'category_user_id' => 'categoryUserId',
            'category_url' => 'categoryUrl',
            'category_depth' => 'categoryDepth',
            'created_at' => 'createdAt',
            'updated_at' => 'updatedAt',
            'deleted_at' => 'deletedAt',
            'is_deleted' => 'isDeleted'
        ];
    }

    /**
     * Returns model as an array (public columns only)
     * @return array
     */
    public function toApiArray()
    {
        return [
            'categoryId' => $this->categoryId,
            'categoryParentId' => $this->categoryParentId,
            'categoryVendorId' => $this->categoryVendorId,
            'categoryName' => $this->categoryName,
            'categoryUrl' => $this->categoryUrl,
            'categoryOrder' => $this->categoryOrder,
            'categoryDepth' => $this->categoryDepth
        ];
    }

    public function beforeDelete()
    {
        $this->_operationMade = self::OP_DELETE;
    }

    /**
     * @return CategoryRules
     */
    private function getValidationRules(): CategoryRules
    {
        return new CategoryRules();
    }

    /**
     * Validate models' attributes
     * @return bool
     */
    public function validation(): bool
    {
        if ($this->_operationMade == self::OP_DELETE) {
            return true;
        }

        $validator = new Validation();

        // Validate English input
        $validator->add(
            'name',
            new Validation\Validator\Callback([
                'callback' => function ($data) {
                    $categoryName = preg_replace('/[\d\s_]/i', '', $data['categoryName']);
                    if (preg_match('/[a-z]/i', $categoryName) == false) {
                        return false;
                    }
                    return true;
                },
                'message' => 'We only support English language'
            ])
        );

        $validator->add(
            'categoryName',
            new AlphaNumericValidator([
                'whiteSpace' => $this->getValidationRules()->allowNameWhiteSpace,
                'underscore' => $this->getValidationRules()->allowNameUnderscore,
                'min' => $this->getValidationRules()->minNameLength,
                'max' => $this->getValidationRules()->maxNameLength,
                'message' => 'Invalid category name',
                'messageMinimum' => 'Category name should be at least 3 characters',
                'messageMaximum' => 'Category name should not exceed 100 characters'
            ])
        );

        $validator->add(
            ['categoryName', 'categoryVendorId'],
            new Validation\Validator\Uniqueness([
                'model' => self::model(true),
                'convert' => function ($values) {
                    $values['categoryVendorId'] = $this->categoryVendorId;
                    return $values;
                },
                'message' => 'Category name already exists'
            ])
        );

        $validator->add(
            'categoryParentId',
            new Validation\Validator\Callback([
                'callback' => function ($data) {
                    if (!empty($data['categoryParentId'])) {
                        return (new UuidUtil())->isValid($data['categoryParentId']);
                    }
                    return true;
                },
                'message' => 'Invalid parent category Id'
            ])
        );

        $validator->add(
            'categoryParentId',
            new ExistenceValidator([
                'model' => self::class,
                'column' => 'categoryId',
                'conditions' => [
                    'where' => 'categoryVendorId = :vendorId:',
                    'bind' => ['vendorId' => $this->categoryVendorId]
                ],
                'message' => 'Parent category does not exist'
            ])
        );

        $validator->add(
            'categoryOrder',
            new Validation\Validator\NumericValidator([
                'min' => $this->getValidationRules()->minOrder,
                'max' => $this->getValidationRules()->maxOrder,
                'message' => 'Category order should be a number'
            ])
        );

        $this->_errorMessages = $messages = $validator->validate([
            'categoryName' => $this->categoryName,
            'categoryParentId' => $this->categoryParentId,
            'categoryOrder' => $this->categoryOrder
        ]);

        return !$messages->count();
    }
}
