<?php

namespace app\common\models;

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
    protected $categoryId;

    /**
     * @var string
     * @Column(column="category_parent_id", type="string", length=36)
     */
    protected $categoryParentId;

    /**
     * @var string
     * @Column(column="category_vendor_id", type="string", length=36, nullable=false)
     */
    protected $categoryVendorId;

    /**
     * @var string $categoryUserId
     * @Column(column="category_user_id", type="string", length=36, nullable=false)
     */
    protected $categoryUserId;

    /**
     * @var integer $categoryOrder
     * @Column(column="category_order", type="integer", length=3, nullable=false)
     */
    protected $categoryOrder = 0;

    /**
     * @var string
     * @Column(column="category_name", type="string", length=100, nullable=false)
     */
    protected $categoryName;

    /**
     * @var string
     * @Column(column="category_url", type="string", length=255)
     */
    protected $categoryUrl;

    /**
     * @var string
     * @Column(column="category_depth", type="integer", length=2, nullable=false)
     */
    public $categoryDepth;

    /**
     * @var string
     * @Column(column="created_at", type="string", nullable=false)
     */
    protected $createdAt;

    /**
     * @var string
     * @Column(column="updated_at", type="string")
     */
    protected $updatedAt;

    /**
     * @var string
     * @Column(column="deleted_at", type="string")
     */
    protected $deletedAt;

    /**
     * @var integer
     * @Column(column="is_deleted", type="integer", length=1, nullable=false)
     */
    protected $isDeleted;

    /**
     * @param string $categoryVendorId
     */
    public function setCategoryVendorId($categoryVendorId)
    {
        $this->categoryVendorId = $categoryVendorId;
    }

    /**
     * @param $categoryUserId
     */
    public function setCategoryUserId($categoryUserId)
    {
        $this->categoryUserId = $categoryUserId;
    }

    /**
     * @param string $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @param string $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @param string $deletedAt
     */
    public function setDeletedAt($deletedAt)
    {
        $this->deletedAt = $deletedAt;
    }

    /**
     * @param integer $isDeleted
     */
    public function setIsDeleted($isDeleted)
    {
        $this->isDeleted = $isDeleted;
    }

    /**
     * @return string
     */
    public function getCategoryId()
    {
        return $this->categoryId;
    }

    /**
     * @return string
     */
    public function getCategoryParentId()
    {
        return $this->categoryParentId;
    }

    /**
     * @return null|int
     */
    public function getCategoryOrder(): int
    {
        return $this->categoryOrder;
    }

    /**
     * @return string
     */
    public function getCategoryVendorId()
    {
        return $this->categoryVendorId;
    }

    /**
     * @return string
     */
    public function getCategoryUserId()
    {
        return $this->categoryUserId;
    }

    /**
     * @return string
     */
    public function getCategoryName()
    {
        return $this->categoryName;
    }

    /**
     * @return string
     */
    public function getCategoryUrl()
    {
        return $this->categoryUrl;
    }

    /**
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return string
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @return string
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    /**
     * @return integer
     */
    public function getIsDeleted()
    {
        return $this->isDeleted;
    }

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
            'categoryId' => $this->getCategoryId(),
            'categoryParentId' => $this->getCategoryParentId(),
            'categoryVendorId' => $this->getCategoryVendorId(),
            'categoryName' => $this->getCategoryName(),
            'categoryUrl' => $this->getCategoryUrl(),
            'categoryOrder' => $this->getCategoryOrder()
        ];
    }

    /**
     * Returns model as an array (internal usage)
     * @param null $columns
     * @return array
     */
    public function toArray($columns = null)
    {
        return [
            'categoryId' => $this->getCategoryId(),
            'categoryParentId' => $this->getCategoryParentId(),
            'categoryName' => $this->getCategoryName(),
            'categoryOrder' => $this->getCategoryOrder(),
            'categoryUserId' => $this->getCategoryUserId(),
            'categoryVendorId' => $this->getCategoryVendorId(),
            'categoryUrl' => $this->getCategoryUrl(),
            'createdAt' => $this->getCreatedAt(),
            'updatedAt' => $this->getUpdatedAt()
        ];
    }

    public function beforeDelete()
    {
        $this->_operationMade = self::OP_DELETE;
    }

    /**
     * @return Config
     */
    private function getValidationConfig(): Config
    {
        return $this->getDI()->getConfig()->application->validation;
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
                'whiteSpace' => $this->getValidationConfig()->categoryNameValidationConfig->allowWhiteSpace,
                'underscore' => $this->getValidationConfig()->categoryNameValidationConfig->allowUnderscore,
                'min' => $this->getValidationConfig()->categoryNameValidationConfig->minNameLength,
                'max' => $this->getValidationConfig()->categoryNameValidationConfig->maxNameLength,
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
                'min' => $this->getValidationConfig()->categoryOrderValidationConfig->minCategoryOrder,
                'max' => $this->getValidationConfig()->categoryOrderValidationConfig->maxCategoryOrder,
                'message' => 'Category order should be a number'
            ])
        );

        $this->_errorMessages = $messages = $validator->validate([
            'categoryName' => $this->getCategoryName(),
            'categoryParentId' => $this->getCategoryParentId(),
            'categoryOrder' => $this->getCategoryOrder()
        ]);

        return $messages->count() ? false : true;
    }
}
