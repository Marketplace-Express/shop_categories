<?php
/**
 * User: Wajdi Jurry
 * Date: 29/12/18
 * Time: 06:13 م
 */

namespace Shop_categories\Collections;

use Phalcon\Mvc\MongoCollection As Collection;
use Shop_categories\Traits\ModelCollectionBehaviorTrait;

abstract class BaseCollection extends Collection
{
    /** @var Collection $instance */
    protected static $instance;

    use ModelCollectionBehaviorTrait;

    public function onConstruct()
    {
        self::$instance = $this;
    }

    /**
     * @param bool $new
     * @return Collection|BaseCollection|Attribute
     */
    public static function model(bool $new = false)
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