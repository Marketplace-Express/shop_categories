<?php
/**
 * User: Wajdi Jurry
 * Date: 11/08/18
 * Time: 09:37 م
 */

namespace Shop_categories\Traits;

use MongoDB\BSON\UTCDateTime;
use Phalcon\Mvc\Collection;
use Phalcon\Mvc\MongoCollection;
use Phalcon\Mvc\Model;

trait ModelBehaviorTrait
{
    public static $dateFormat = 'Y-m-d G:i:s';

    /**
     * @throws \Exception
     */
    public function defaultBehavior()
    {
        $modelType = self::getType();
        if ($modelType === Model::class) {
            $this->addBehavior(new Model\Behavior\SoftDelete([
                'field' => 'isDeleted',
                'value' => 1
            ]));

            $this->addBehavior(new Model\Behavior\SoftDelete([
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

        } elseif ($modelType === MongoCollection::class) {

            $this->addBehavior(new Collection\Behavior\SoftDelete([
                'field' => 'is_deleted',
                'value' => true
            ]));

            $this->addBehavior(new Collection\Behavior\SoftDelete([
                'field' => 'deleted_at',
                'value' => new UTCDateTime()
            ]));

            $this->addBehavior(new Collection\Behavior\Timestampable([
                'beforeCreate' => [
                    'field' => 'created_at',
                    'generator' => function() {
                        return new UTCDateTime();
                    }
                ],
                'beforeUpdate' => [
                    'field' => 'updated_at',
                    'generator' => function() {
                        return new UTCDateTime();
                    }
                ]
            ]));

        } else {
            throw new \Exception('Use ModelBehaviorTrait only with Model and Collection models types');
        }
    }
}