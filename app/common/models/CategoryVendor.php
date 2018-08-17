<?php

namespace Shop_categories\Models;

use Phalcon\Mvc\Model\ResultInterface;

/**
 * @method  moveAsLast(ResultInterface $parent)
 * @method  saveNode(array $data)
 */
class CategoryVendor extends \Phalcon\Mvc\Model
{

    /**
     * @var string
     * @Column(column="category_id", type="string", length=36, nullable=false)
     */
    public $categoryId;

    /**
     * @var string
     * @Column(column="vendor_id", type="string", length=36, nullable=false)
     */
    public $vendorId;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("shop_categories");
        $this->setSource("category_vendor");

        $this->hasMany(
            'categoryId',
            'Category',
            'categoryId',
            [
                'alias' => 'vendorCategories'
            ]
        );
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'category_vendor';
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
            'vendor_id' => 'vendorId'
        ];
    }
}
