<?php
/**
 * User: Wajdi Jurry
 * Date: 14/09/18
 * Time: 04:31 م
 */

namespace Shop_categories\Models\Behaviors;

use Phalcon\Db\AdapterInterface;
use Phalcon\Mvc\Model\Behavior;
use Phalcon\Mvc\Model\BehaviorInterface;
use Phalcon\Mvc\Model\MetaData;
use Phalcon\Mvc\ModelInterface;
use Shop_categories\Traits\AdjacencyModelEventManagerTrait;
use Shop_categories\Helpers\AdjacencyListModelHelper;

class AdjacencyListModelBehavior extends Behavior implements BehaviorInterface, BaseBehavior
{
    use AdjacencyModelEventManagerTrait;

    /**
     * @var AdapterInterface|null
     */
    private $db;

    private $params;
    private $itemIdAttribute = 'item_id';
    private $parentIdAttribute = 'parent_id';
    private $orderByAttribute = null;
    private $isDeletedAttribute = null;
    private $isDeletedValue = null;
    private $subItemsSlug = 'children';
    private $noParentValue = null;

    /** @var ModelInterface $owner */
    private $owner;

    /**
     * AdjacencyListModelBehavior constructor.
     * @param array $params
     */
    public function __construct(array $params)
    {
        if (isset($params['itemIdAttribute'])) {
            $this->itemIdAttribute = $params['itemIdAttribute'];
        }
        if (isset($params['parentIdAttribute'])) {
            $this->parentIdAttribute = $params['parentIdAttribute'];
        }
        if (isset($params['orderByAttribute'])) {
            $this->orderByAttribute = $params['orderByAttribute'];
        }
        if (isset($params['isDeletedAttribute'])) {
            $this->isDeletedAttribute  = $params['isDeletedAttribute'];
        }
        if (isset($params['isDeletedValue'])) {
            $this->isDeletedValue = $params['isDeletedValue'];
        }
        if (isset($params['subItemsSlug'])) {
            $this->subItemsSlug = $params['subItemsSlug'];
        }
        if (isset($params['noParentValue'])) {
            $this->noParentValue = $params['noParentValue'];
        }

        $this->params = [
            'itemIdAttribute' => $this->itemIdAttribute,
            'parentIdAttribute' => $this->parentIdAttribute,
            'subItemsSlug' => $this->subItemsSlug,
            'noParentValue' => $this->noParentValue
        ];
    }

    /**
     * Gets DB handler.
     *
     * @param ModelInterface $model
     * @return AdapterInterface
     *
     * @codeCoverageIgnore
     * @throws \Exception
     */
    private function getDbHandler(ModelInterface $model)
    {
        if (!$this->db instanceof AdapterInterface) {
            if ($model->getDi()->has('db')) {
                $db = $model->getDi()->getShared('db');
                if (!$db instanceof AdapterInterface) {
                    throw new \Exception('The "db" service which was obtained from DI is invalid adapter.');
                }
                $this->db = $db;
            } else {
                throw new \Exception('Undefined database handler.');
            }
        }

        return $this->db;
    }

    /**
     * Calls a method when it's missing in the model
     *
     * @param ModelInterface $model
     * @param string $method
     * @param null $arguments
     * @return mixed|null|string
     *
     * @codeCoverageIgnore
     * @throws \Exception
     */
    public function missingMethod(ModelInterface $model, $method, $arguments = null)
    {
        if (!method_exists($this, $method)) {
            return null;
        }
        $this->getDbHandler($model);
        $this->setOwner($model);
        return call_user_func_array([$this, $method], $arguments);
    }

    /**
     * @return ModelInterface
     * @codeCoverageIgnore
     */
    private function getOwner()
    {
        if (!$this->owner instanceof ModelInterface) {
            trigger_error("Owner isn't a valid ModelInterface instance.", E_USER_WARNING);
        }

        return $this->owner;
    }

    /**
     * @param ModelInterface $owner
     *
     * @codeCoverageIgnore
     */
    private function setOwner(ModelInterface $owner)
    {
        $this->owner = $owner;
    }

    /**
     * @return MetaData
     *
     * @codeCoverageIgnore
     * @throws \Exception
     */
    private function getAttributes()
    {
        if ($this->getOwner()->getDi()->has('modelsMetadata')) {
            return $this->getOwner()->getDi()->getShared('modelsMetadata')->readColumnMap($this->getOwner());
        }

        throw new \Exception('Model missing columnMap method');
    }

    /**
     * @param string $conditions
     * @param array $bind
     * @return \Phalcon\Mvc\Model\ResultsetInterface
     *
     * @codeCoverageIgnore
     */
    public function find(string $conditions, array $bind)
    {
        return $this->getOwner()::find([
            'conditions' => $conditions,
            'bind' => $bind
        ]);
    }

    /**
     * Check if target is descendant of item
     * @param $itemId
     * @param $targetId
     * @return bool
     * @throws \Exception
     */
    public function isDescendant($itemId, $targetId)
    {
        $descendants = array_column($this->descendants($itemId, true, false), $this->itemIdAttribute);
        if (in_array($targetId, $descendants)) {
            return true;
        }
        return false;
    }

    /**
     * Get roots
     * @param bool $toArray
     * @return array|\Phalcon\Mvc\Model\ResultsetInterface
     * @throws \Exception
     */
    public function roots(bool $toArray = true)
    {
        $conditions = $this->parentIdAttribute . ' = :noParentValue:';
        $bind['noParentValue'] = $this->noParentValue;
        if (isset($this->isDeletedAttribute) && isset($this->isDeletedValue)) {
            $conditions .= ' AND ' . $this->isDeletedAttribute . ' <> :isDeletedValue:';
            $bind['isDeletedValue'] = $this->isDeletedValue;
        }

        $result = $this->find($conditions, $bind);

        if (count($result)) {
            return ($toArray) ? $result->toArray() : $result;
        }

        throw new \Exception("No roots found", 404);
    }

    /**
     * Get item parent(s)
     * @param string $itemId
     * @param bool $recursive
     * @param bool $toArray
     * @param bool $oneParent
     * @param bool $addSelf
     * @return array|bool
     * @throws \Exception
     */
    public function parents(string $itemId, bool $toArray = true, $recursive = true, bool $oneParent = false, bool $addSelf = false)
    {
        $columns = $this->getAttributes()[1];
        $query = sprintf(
            'WITH RECURSIVE category_path (%s)
                AS (SELECT %s FROM %s
                    WHERE %s = :itemId %s
                    UNION ALL SELECT %s
                        FROM category_path AS cp
                    JOIN category AS c ON cp.%s = c.%s %s)
                    SELECT *
                    FROM category_path ORDER BY %s ASC;',
            implode(',', $columns),
            implode(',', $columns),
            $this->getOwner()->getSource(),
            $columns[$this->itemIdAttribute],
            (isset($this->isDeletedAttribute) && isset($this->isDeletedValue)) ? 'AND ' . $columns[$this->isDeletedAttribute] . ' = :isDeletedValue' : '',
            implode(',', array_map(function($column){return 'c.'.$column;}, $columns)),
            $columns[$this->parentIdAttribute],
            $columns[$this->itemIdAttribute],
            (isset($this->isDeletedAttribute) && isset($this->isDeletedValue)) ? 'AND c.' . $columns[$this->isDeletedAttribute] . ' = :isDeletedValue' : '',
            $columns[$this->orderByAttribute]
        );
        $query = $this->getOwner()->getReadConnection()->query($query, ['itemId' => $itemId, 'isDeletedValue' => $this->isDeletedValue]);
        $query->setFetchMode(\PDO::FETCH_CLASS, get_class($this->getOwner()));
        if ($result = $query->fetchAll()) {
            if ($toArray) {
                $result = array_map(function ($row) use ($columns) {
                    return $row->toArray();
                }, $result);

                $result = ($recursive) ? (new AdjacencyListModelHelper($result, $this->params))->prepare() : $result;
            }
            return $result;
        }

        throw new \Exception("Item not found or may be deleted", 404);
    }

    /**
     * Get item descendants
     * @param string $itemId
     * @param bool $toArray
     * @param bool $recursive
     * @return array|bool
     * @throws \Exception
     */
    public function descendants(string $itemId, bool $toArray = true, $recursive = true)
    {
        $columns = $this->getAttributes()[1];
        $query = sprintf(
            'WITH RECURSIVE category_path (%s)
                AS (SELECT %s FROM %s
                    WHERE %s = :itemId %s
                    UNION ALL SELECT %s
                        FROM category_path AS cp
                    JOIN category AS c ON cp.%s = c.%s %s)
                    SELECT *
                    FROM category_path ORDER BY %s ASC;',
            implode(',', $columns),
            implode(',', $columns),
            $this->getOwner()->getSource(),
            $columns[$this->itemIdAttribute],
            (isset($this->isDeletedAttribute) && isset($this->isDeletedValue)) ? 'AND ' . $columns[$this->isDeletedAttribute] . ' = :isDeletedValue' : '',
            implode(',', array_map(function($column){return 'c.'.$column;}, $columns)),
            $columns[$this->itemIdAttribute],
            $columns[$this->parentIdAttribute],
            (isset($this->isDeletedAttribute) && isset($this->isDeletedValue)) ? 'AND c.' . $columns[$this->isDeletedAttribute] . ' = :isDeletedValue' : '',
            $columns[$this->orderByAttribute]
        );
        $query = $this->getOwner()->getReadConnection()->query($query, ['itemId' => $itemId, 'isDeletedValue' => $this->isDeletedValue]);
        $query->setFetchMode(\PDO::FETCH_CLASS, get_class($this->getOwner()));
        if ($result = $query->fetchAll()) {
            if ($toArray) {
                $result = array_map(function ($row) use ($columns) {
                    return $row->toArray();
                }, $result);

                $result = ($recursive) ? (new AdjacencyListModelHelper($result, $this->params))->prepare() : $result;
            }
            return $result;
        }

        throw new \Exception("Item not found or maybe deleted", 404);
    }

    /**
     * Get item children
     * @param string $itemId
     * @param bool $toArray
     * @return array|\Phalcon\Mvc\Model\ResultsetInterface
     * @throws \Exception
     */
    public function children(string $itemId, bool $toArray = true)
    {
        $conditions = $this->parentIdAttribute . ' = :parentId:';
        $bind['parentId'] = $itemId;
        if (isset($this->isDeletedAttribute) && isset($this->isDeletedValue)) {
            $conditions .= ' AND '. $this->isDeletedAttribute . ' <> :isDeletedValue:';
            $bind['isDeletedValue'] = $this->isDeletedValue;
        }

        $result = $this->find($conditions, $bind);

        if (count($result)) {
            return ($toArray) ? $result->toArray() : $result;
        }

        throw new \Exception("Item not found or maybe deleted", 404);
    }

    /**
     * @param string $itemId
     * @return bool
     * @throws \Exception
     */
    public function cascadeDelete(string $itemId)
    {
        $descendants = $this->descendants($itemId, false);
        $this->db->begin();
        if ($descendants) {
            foreach ($descendants as $item) {
                if (!$item->delete()) {
                    $this->db->rollback();
                    throw new \Exception("Item {$item->{"get".ucfirst($this->itemIdAttribute)}()} could not be deleted");
                }
            }
            $this->db->commit();
            return true;
        }
        throw new \Exception("Item not found or maybe deleted", 404);
    }

    /**
     * @param array $array
     * @return array
     *
     * @codeCoverageIgnore
     * @throws \Exception
     */
    public function recursive(array $array)
    {
        return (new AdjacencyListModelHelper($array, $this->params))->prepare();
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