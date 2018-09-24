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

class AdjacencyListModelBehavior extends Behavior implements BehaviorInterface
{
    use AdjacencyModelEventManagerTrait;

    /**
     * @var AdapterInterface|null
     */
    private $adapter;

    private $params;
    private $itemIdAttribute = 'itemId';
    private $parentIdAttribute = 'parentId';
    private $subItemsSlug = 'children';
    private $noParentValue = null;

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
     * @throws \Exception
     */
    private function getDbHandler(ModelInterface $model)
    {
        if (!$this->adapter instanceof AdapterInterface) {
            if ($model->getDi()->has('db')) {
                $db = $model->getDi()->getShared('db');
                if (!$db instanceof AdapterInterface) {
                    throw new \Exception('The "db" service which was obtained from DI is invalid adapter.');
                }
                $this->adapter = $db;
            } else {
                throw new \Exception('Undefined database handler.');
            }
        }

        return $this->adapter;
    }

    /**
     * Calls a method when it's missing in the model
     *
     * @param ModelInterface $model
     * @param string $method
     * @param null $arguments
     * @return mixed|null|string
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
     */
    private function getOwner()
    {
        if (!$this->owner instanceof ModelInterface) {
            trigger_error("Owner isn't a valid ModelInterface instance.", E_USER_WARNING);
        }

        return $this->owner;
    }

    private function setOwner(ModelInterface $owner)
    {
        $this->owner = $owner;
    }

    /**
     * @param ModelInterface $model
     * @return MetaData
     * @throws \Exception
     */
    private function getAttributes(ModelInterface $model)
    {
        if ($model->getDi()->has('modelsMetadata')) {
            return $model->getDi()->getShared('modelsMetadata')->readColumnMap($model);
        }

        throw new \Exception('Model missing columnMap method');
    }

    public function roots()
    {
        return $this->getOwner()::find([
            'conditions' => $this->parentIdAttribute . ' = :noParentValue:',
            'bind' => ['noParentValue' => $this->noParentValue]
        ]);
    }

    /**
     * Get item parent(s)
     * @param null $itemId
     * @param bool $oneParent
     * @return array
     * @throws \Exception
     */
    public function parents($itemId = null, bool $oneParent = false)
    {
        $columns = $this->getAttributes($this->getOwner())[1];
        $query = sprintf(
            'WITH RECURSIVE category_path (%s)
                AS (SELECT %s FROM %s
                    WHERE %s = :itemId
                    UNION ALL SELECT %s
                        FROM category_path AS cp
                    JOIN category AS c ON cp.%s = c.%s)
                    SELECT *
                    FROM category_path %s OFFSET 1;',
            implode(',', $columns),
            implode(',', $columns),
            $this->getOwner()->getSource(),
            $columns[$this->itemIdAttribute],
            implode(',', array_map(function($column){return 'c.'.$column;}, $columns)),
            $columns[$this->parentIdAttribute],
            $columns[$this->itemIdAttribute],
            ($oneParent) ? 'LIMIT 2' : 'LIMIT 10'
        );
        $result = $this->getOwner()->getReadConnection()->fetchAll(
            $query,
            \PDO::FETCH_ASSOC,
            [
                'itemId' => $itemId
            ]
        );
        if ($result) {
            $result = array_map(function($row) use ($columns) {
                return array_combine(array_keys($columns), array_values($row));
            }, $result);

            $result = (new AdjacencyListModelHelper($result, $this->params))->parents();
        }

        return $result;
    }

    /**
     * Get item parent(s)
     * @param null $itemId
     * @return array
     * @throws \Exception
     */
    public function descendants($itemId = null)
    {
        $columns = $this->getAttributes($this->getOwner())[1];
        $query = sprintf(
            'WITH RECURSIVE category_path (%s)
                AS (SELECT %s FROM %s
                    WHERE %s = :itemId
                    UNION ALL SELECT %s
                        FROM category_path AS cp
                    JOIN category AS c ON cp.%s = c.%s)
                    SELECT *
                    FROM category_path;',
            implode(',', $columns),
            implode(',', $columns),
            $this->getOwner()->getSource(),
            $columns[$this->itemIdAttribute],
            implode(',', array_map(function($column){return 'c.'.$column;}, $columns)),
            $columns[$this->itemIdAttribute],
            $columns[$this->parentIdAttribute]
        );
        $result = $this->getOwner()->getReadConnection()->fetchAll(
            $query,
            \PDO::FETCH_ASSOC,
            [
                'itemId' => $itemId
            ]
        );
        if ($result) {
            $result = array_map(function($row) use ($columns) {
                return array_combine(array_keys($columns), array_values($row));
            }, $result);

            $result = (new AdjacencyListModelHelper($result, $this->params))->parents();
        }

        return $result;
    }

    public function children(string $itemId)
    {
        return $this->getOwner()::find([
            'conditions' => $this->parentIdAttribute . ' = :parentId:',
            'bind' => ['parentId' => $itemId]
        ])->toArray();
    }

    /**
     * @param array $array
     * @return array
     * @throws \Exception
     */
    public function recursive(array $array)
    {
        return (new AdjacencyListModelHelper($array, $this->params))->all();
    }

    /**
     * This method receives the notifications from the EventsManager
     *
     * @param string $type
     * @param \Phalcon\Mvc\ModelInterface $model
     */
    public function notify($type, ModelInterface $model)
    {
        // TODO: Implement notify() method.
    }
}