<?php

namespace Shop_categories\Models;

use Phalcon\Validation;
use Phalcon\Validation\Validator\AlphaNumericValidator;
use Shop_categories\Models\Behaviors\AdjacencyListModelBehavior;
use Shop_categories\Utils\UuidUtil;
use Shop_categories\Validators\ExistenceValidator;

/**
 * Category
 * 
 * @package Shop_categories\Models
 * @autogenerated by Phalcon Developer Tools
 * @date 2018-09-13, 19:46:12
 */
class Category extends Base
{
    /**
     * @var string
     * @Primary
     * @Column(column="category_id", type="string", length=36, nullable=false)
     */
    protected $categoryId;

    /**
     * @var string
     * @Column(column="category_parent_id", type="string", length=36, nullable=true)
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
     * @Column(column="category_name", type="string", length=255, nullable=false)
     */
    protected $categoryName;

    /**
     * @var string
     * @Column(column="created_at", type="string", nullable=false)
     */
    protected $createdAt;

    /**
     * @var string
     * @Column(column="updated_at", type="string", nullable=true)
     */
    protected $updatedAt;

    /**
     * @var string
     * @Column(column="deleted_at", type="string", nullable=true)
     */
    protected $deletedAt;

    /**
     * @var integer
     * @Column(column="is_deleted", type="integer", length=1, nullable=false)
     */
    protected $isDeleted;

    /**
     * @param string $categoryId
     */
    public function setCategoryId($categoryId)
    {
        $this->categoryId = $categoryId;
    }

    /**
     * @param string $categoryParentId
     */
    public function setCategoryParentId($categoryParentId)
    {
        $this->categoryParentId = $categoryParentId;
    }

    /**
     * @param string $categoryName
     */
    public function setCategoryName($categoryName)
    {
        $this->categoryName = $categoryName;
    }

    /**
     * @param int $categoryOrder
     */
    public function setCategoryOrder(?int $categoryOrder): void
    {
        $this->categoryOrder = $categoryOrder;
    }

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
     * @param array $attributes
     * @return Category
     */
    public function setAttributes(array $attributes): self
    {
        foreach ($attributes as $attribute => $value) {
            $this->$attribute = $value;
        }
        return $this;
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

        $this->addBehavior(new AdjacencyListModelBehavior([
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
            'categoryName' => $this->getCategoryName(),
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
            'updatedAt' => $this->getUpdatedAt()
        ];
    }

    public function beforeDelete()
    {
        $this->_operationMade = self::OP_DELETE;
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
        $validator->add(
            'categoryName',
            new AlphaNumericValidator([
                'whiteSpace' => $this->di->getConfig()->application->categoryNameValidationConfig->allowWhiteSpace,
                'underscore' => $this->di->getConfig()->application->categoryNameValidationConfig->allowUnderscore,
                'min' => $this->di->getConfig()->application->categoryNameValidationConfig->minNameLength,
                'max' => $this->di->getConfig()->application->categoryNameValidationConfig->maxNameLength,
                'message' => 'Invalid category name',
                'messageMinimum' => 'Category name should be at least 3 characters',
                'messageMaximum' => 'Category name should not exceed 100 characters'
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
                'min' => $this->di->getConfig()->application->minCategoryOrder,
                'max' => $this->di->getConfig()->application->maxCategoryOrder,
                'allowFloat' => $this->di->getConfig()->application->allowFloat,
                'allowSign' => $this->di->getConfig()->application->allowSign,
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
