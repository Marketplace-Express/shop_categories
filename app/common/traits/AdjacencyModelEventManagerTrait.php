<?php

namespace app\common\traits;

use Phalcon\Di;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Mvc\ModelInterface;

/**
 * Phalcon\Traits\EventManagerAwareTrait
 *
 * Trait for event processing
 *
 * @package Phalcon\Traits
 */

trait AdjacencyModelEventManagerTrait
{
    /**
     * @var EventsManager
     */
    protected $eventsManager = null;

    /**
     * set event manager
     *
     * @param EventsManager $manager
     */
    public function setEventsManager(EventsManager $manager)
    {
        $this->eventsManager = $manager;
    }

    /**
     * return event manager
     *
     * @return EventsManager | null
     */
    public function getEventsManager()
    {
        if (!empty($this->eventsManager)) {
            $manager =  $this->eventsManager;
        } elseif (Di::getDefault()->has('eventsManager')) {
            $manager = Di::getDefault()->get('eventsManager');
        }

        if (isset($manager) && $manager instanceof EventsManager) {
            return $manager;
        }

        return null;
    }

    /**
     * Checking if event manager is defined - fire event
     *
     * @param string $event
     * @param object $source
     * @param mixed $data
     * @param boolean $cancelable
     *
     */
    public function fire($event, $source, $data = null, $cancelable = true)
    {
        if ($manager = $this->getEventsManager()) {
            $manager->fire($event, $source, $data, $cancelable);
        }
    }

    /**
     * This method receives the notifications from the EventsManager
     *
     * @param string $type
     * @param \Phalcon\Mvc\ModelInterface $model
     *
     * @codeCoverageIgnore
     * @throws \Exception
     */
    public function notify($type, ModelInterface $model)
    {
        $this->setOwner($model);
        $this->owner = $model;
        switch ($type) {
            case 'beforeValidationOnUpdate':
                $categoryId = $model->{"get".ucfirst($this->itemIdAttribute)}();
                $parentId = $model->{"get".ucfirst($this->parentIdAttribute)}();
                $isDeleted = $model->{"get".ucfirst($this->isDeletedAttribute)}();
                if (!empty($parentId) && !boolval($isDeleted)) {
                    if ($this->isDescendant($categoryId, $parentId)) {
                        throw new \Exception('Target parent should not be descendant of this category', 400);
                    }
                }
                break;
        }
    }
}
