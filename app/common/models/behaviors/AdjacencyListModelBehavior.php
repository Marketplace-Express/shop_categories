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
use Phalcon\Mvc\ModelInterface;
use Shop_categories\DBTools\Enums\QueryOperatorsEnum;
use Shop_categories\DBTools\QueryBuilder;
use Shop_categories\DBTools\RecursiveQueryBuilder;
use Shop_categories\Traits\AdjacencyModelEventManagerTrait;

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
            /** @noinspection PhpUndefinedMethodInspection */
            if ($model->getDi()->has('db')) {
                /** @noinspection PhpUndefinedMethodInspection */
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
     * @return array
     *
     * @codeCoverageIgnore
     * @throws \Exception
     */
    private function getAttributes(): array
    {
        /** @noinspection PhpUndefinedMethodInspection */
        if ($this->getOwner()->getDi()->has('modelsMetadata')) {
            /** @noinspection PhpUndefinedMethodInspection */
            return $this->getOwner()->getDi()->getShared('modelsMetadata')->readColumnMap($this->getOwner())[1];
        }

        throw new \Exception('Model missing columnMap method');
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
        $descendants = array_map(function($category){ return $category->toArray(); }, $this->descendants($itemId));
        $descendants = array_column($descendants, $this->itemIdAttribute);
        if (in_array($targetId, $descendants)) {
            return true;
        }
        return false;
    }

    /**
     * Get roots
     * @param array $additionalConditions
     * @return array|\Phalcon\Mvc\Model\ResultsetInterface
     * @throws \Exception
     */
    public function roots(array $additionalConditions = [])
    {
        $queryBuilder = new QueryBuilder(
            $this->getOwner()->getSource(),
            $this->getAttributes(),
            [
                [
                    'CONDITIONS' => array_merge([
                        $this->isDeletedAttribute => [QueryOperatorsEnum::OP_EQUALS => $this->isDeletedValue],
                        $this->parentIdAttribute => [QueryOperatorsEnum::OP_IS_NULL => '']
                    ], $additionalConditions)
                ]
            ],
            [
                'column' => $this->getAttributes()[$this->orderByAttribute],
                'direction' => 'ASC'
            ]
        );
        $query = $this->getOwner()->getReadConnection()->query($queryBuilder->getQuery(), $queryBuilder->getBinds());
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        $query->setFetchMode(\PDO::FETCH_CLASS, get_class($this->getOwner()));
        $result = $query->fetchAll();

        if (count($result)) {
            return $result;
        }

        throw new \Exception("No roots found", 404);
    }

    /**
     * Get item parent(s)
     * @param string $itemId
     * @param array $additionalConditions
     * @param bool $oneParent
     * @param bool $addSelf
     * @return array|bool
     * @throws \Exception
     */
    public function parents(string $itemId, array $additionalConditions = [], bool $oneParent = false, bool $addSelf = false)
    {
        $queryBuilder = new RecursiveQueryBuilder(
            $this->getOwner()->getSource(),
            $this->getAttributes(),
            'category_path',
            [
                [
                    'CONDITIONS' => array_merge([
                        'category_id' => [QueryOperatorsEnum::OP_EQUALS => $itemId],
                        'category_parent_id' => [QueryOperatorsEnum::OP_IS_NOT_NULL => ''],
                        'is_deleted' => [QueryOperatorsEnum::OP_EQUALS => $this->isDeletedValue]
                    ], $additionalConditions)
                ],
                [
                    'UNION' => [
                        'table' => 'category_path',
                        'alias' => 'cp',
                        'columnsAlias' => 'c'
                    ]
                ],
                [
                    'JOIN' => [
                        'table' => 'category',
                        'alias' => 'c',
                        'conditions' => [
                            'c.category_id' => [QueryOperatorsEnum::OP_EQUALS => 'cp.category_parent_id', 'process' => false],
                            'c.is_deleted' => [QueryOperatorsEnum::OP_EQUALS => $this->isDeletedValue]
                        ]
                    ]
                ]
            ],
            [
                'column' => $this->getAttributes()[$this->orderByAttribute],
                'direction' => 'ASC'
            ],
            ($oneParent && $addSelf) ? ['limit' => 2, 'offset' => 0] : (($oneParent) ? ['limit' => 1, 'offset' => 1] : [])
        );
        $query = $this->getOwner()->getReadConnection()->query($queryBuilder->getQuery(), $queryBuilder->getBinds());
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        $query->setFetchMode(\PDO::FETCH_CLASS, get_class($this->getOwner()));
        $result = $query->fetchAll();
        return $result;
    }

    /**
     * Get item descendants
     * @param string $itemId
     * @param array $additionalConditions
     * @return array|bool
     * @throws \Exception
     */
    public function descendants(string $itemId, array $additionalConditions = [])
    {
        $columns = $this->getAttributes();
        $queryBuilder = new RecursiveQueryBuilder(
            $this->getOwner()->getSource(),
            $columns,
            'category_path',
            [
                [
                    'CONDITIONS' => array_merge([
                            'category_id' => [QueryOperatorsEnum::OP_EQUALS => $itemId],
                            'is_deleted' => [QueryOperatorsEnum::OP_EQUALS => $this->isDeletedValue]
                    ], $additionalConditions)
                ],
                [
                    'UNION' => [
                        'type' => 'ALL',
                        'table' => 'category_path',
                        'alias' => 'cp',
                        'columnsAlias' => 'c'
                    ]
                ],
                [
                    'JOIN' => [
                        'table' => 'category',
                        'alias' => 'c',
                        'conditions' => [
                            'c.category_parent_id' => [QueryOperatorsEnum::OP_EQUALS => 'cp.category_id', 'process' => false],
                            'c.is_deleted' => [QueryOperatorsEnum::OP_EQUALS => false]
                        ]
                    ]
                ]
            ],
            [
                'column' => $this->getAttributes()[$this->orderByAttribute],
                'direction' => 'ASC'
            ]
        );
        $query = $this->getOwner()->getReadConnection()->query($queryBuilder->getQuery(), $queryBuilder->getBinds());
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        $query->setFetchMode(\PDO::FETCH_CLASS, get_class($this->getOwner()));
        $result = $query->fetchAll();
        return $result;
    }

    /**
     * Get item children
     * @param string $itemId
     * @param array $additionalConditions
     * @return array|\Phalcon\Mvc\Model\ResultsetInterface
     * @throws \Exception
     */
    public function children(string $itemId, array $additionalConditions = [])
    {
        $queryBuilder = new QueryBuilder(
            $this->getOwner()->getSource(),
            $this->getAttributes(),
            [
                [
                    'CONDITIONS' => array_merge([
                        $this->parentIdAttribute => [QueryOperatorsEnum::OP_EQUALS => $itemId],
                        $this->isDeletedAttribute => [QueryOperatorsEnum::OP_EQUALS => $this->isDeletedValue]
                    ], $additionalConditions)
                ]
            ],
            [
                'column' => $this->getAttributes()[$this->orderByAttribute],
                'direction' => 'ASC'
            ]
        );
        $query = $this->getOwner()->getReadConnection()->query($queryBuilder->getQuery(), $queryBuilder->getBinds());
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        $query->setFetchMode(\PDO::FETCH_CLASS, get_class($this->getOwner()));
        $result = $query->fetchAll();
        if (count($result)) {
            return $result;
        }

        throw new \Exception("Item not found or maybe deleted", 404);
    }

    /**
     * @param array $additionalConditions
     * @return array
     * @throws \Exception
     */
    public function getItems(array $additionalConditions = [])
    {
        $queryBuilder = new QueryBuilder(
            $this->getOwner()->getSource(),
            $this->getAttributes(),
            [
                [
                    'CONDITIONS' => array_merge([
                        $this->isDeletedAttribute => [QueryOperatorsEnum::OP_EQUALS => $this->isDeletedValue]
                    ], $additionalConditions)
                ]
            ],
            [
                'column' => $this->getAttributes()[$this->orderByAttribute],
                'direction' => 'ASC'
            ]
        );
        $query = $this->getOwner()->getReadConnection()->query($queryBuilder->getQuery(), $queryBuilder->getBinds());
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        $query->setFetchMode(\PDO::FETCH_CLASS, get_class($this->getOwner()));
        $result = $query->fetchAll();
        return $result;
    }

    /**
     * @param string $itemId
     * @param array $additionalConditions
     * @return bool
     * @throws \Exception
     */
    public function deleteCascade(string $itemId, array $additionalConditions = [])
    {
        $descendants = $this->descendants($itemId, $additionalConditions);
        if ($descendants) {
            $this->db->begin();
            foreach ($descendants as $item) {
                if (!$item->delete()) {
                    $this->db->rollback();
                    throw new \Exception("Item {$item->{"get".ucfirst($this->itemIdAttribute)}()} could not be deleted");
                }
            }
            return $this->db->commit();
        }
        throw new \Exception("Item not found or maybe deleted", 404);
    }
}