<?php
/**
 * User: Wajdi Jurry
 * Date: 21/12/18
 * Time: 04:59 م
 */

namespace app\common\models;

use Phalcon\Mvc\Model;
use app\common\interfaces\BaseModelInterface;
use app\common\traits\ModelCollectionBehaviorTrait;

abstract class BaseModel extends Model implements BaseModelInterface
{
    use ModelCollectionBehaviorTrait;

    protected static $instance;

    public function onConstruct()
    {
        self::$instance = $this;
    }

    /**
     * @param bool $new
     * @return Category
     */
    public static function model(bool $new = false): self
    {
        return !empty(self::$instance) && !$new ? self::$instance : new static;
    }

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
        foreach (parent::getMessages() as $message) {
            if (is_array($field = $message->getField())) {
                $field = $field[0];
            }
            $messages[$field] = $message->getMessage();
        }
        return $messages;
    }

    /**
     * Returns model type
     * @return string
     */
    public static function getType()
    {
        return Model::class;
    }
}
