<?php
/**
 * User: Wajdi Jurry
 * Date: 29/12/18
 * Time: 06:13 م
 */

namespace Shop_categories\Models;

use Phalcon\Mvc\MongoCollection As Collection;
use Shop_categories\Traits\ModelBehaviorTrait;

abstract class BaseCollection extends Collection
{
    /** @var Attribute $instance */
    protected static $instance;

    use ModelBehaviorTrait;

    public function onConstruct()
    {
        self::$instance = $this;
    }

    /**
     * @param bool $new
     * @return Attribute
     */
    public static function model(bool $new = false): Collection
    {
        return (self::$instance && !$new) ? self::$instance : new static;
    }

    /**
     * Returns model's error messages
     * @return array
     */
    public function getMessages(): array
    {
        $messages = [];
        foreach (parent::getMessages() as $message) {
            if (is_array($field = $message->getField())) {
                $field = $message->getField()[0];
            }
            $messages[$field] = $message->getMessage();
        }
        return $messages;
    }

    /**
     * Returns model type
     * @return string
     */
    public function getType()
    {
        return Collection::class;
    }
}