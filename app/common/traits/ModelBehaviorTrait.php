<?php
/**
 * User: Wajdi Jurry
 * Date: 11/08/18
 * Time: 09:37 م
 */

namespace Shop_categories\Traits;

use Phalcon\Mvc\Model;
use Phalcon\Validation;

trait ModelBehaviorTrait
{
    public static $dateFormat = 'Y-m-d G:i:s';
    public function defaultBehavior()
    {
        $this->addBehavior(new  Model\Behavior\SoftDelete([
            'field' => 'isDeleted',
            'value' => 1
        ]));

        $this->addBehavior(new  Model\Behavior\SoftDelete([
            'field' => 'deletedAt',
            'value' => date(self::$dateFormat, time())
        ]));

        $this->addBehavior(new Model\Behavior\Timestampable([
            'beforeValidationOnCreate' => [
                'field' => 'created_at',
                'format' => self::$dateFormat
            ],
            'beforeValidationOnUpdate' => [
                'field' => 'updated_at',
                'format' => self::$dateFormat
            ]
        ]));
    }
}