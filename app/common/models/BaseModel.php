<?php
/**
 * User: Wajdi Jurry
 * Date: 21/12/18
 * Time: 04:59 م
 */

namespace Shop_categories\models;


use Phalcon\Mvc\Model;
use Shop_categories\Interfaces\BaseModelInterface;

abstract class BaseModel extends Model implements BaseModelInterface
{
    const DELETE_OPERATION = 'delete';
    const CREATE_OPERATION = 'create';
    const UPDATE_OPERATION = 'update';

    protected $operation;

    protected static $instance;

    public function onConstruct()
    {
        self::$instance = $this;
        $this->setSchema("shop_categories");
    }

    public static function model(){}

    /**
     * @param \Phalcon\DiInterface|null $dependencyInjector
     * @return \Phalcon\Mvc\Model\Criteria
     */
    public static function query(\Phalcon\DiInterface $dependencyInjector = null)
    {
        $query = parent::query($dependencyInjector);
        $query->where('isDeleted = false');
        return $query;
    }

    /**
     * Returns model's error messages
     * @param null $filter
     * @return array
     */
    public function getMessages($filter = null): array
    {
        $messages = [];
        foreach (parent::getMessages($filter) as $message) {
            $messages[$message->getField()] = $message->getMessage();
        }
        return $messages;
    }
}