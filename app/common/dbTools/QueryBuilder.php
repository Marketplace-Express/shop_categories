<?php
/**
 * User: Wajdi Jurry
 * Date: 05/12/18
 * Time: 11:10 م
 */

namespace app\common\dbTools;

use app\common\exceptions\OperationFailedException;
use app\common\dbTools\enums\SchemaQueryOperatorsEnum;

class QueryBuilder
{
    /** @var string $table */
    public $table;

    /** @var array $columns */
    public $columns;

    /** @var array $conditions */
    public $conditions;

    /** @var array $options */
    public $options;

    /** @var array $orderBy */
    protected $orderBy;

    /** @var array $limit */
    protected $limit;

    /** @var string $alias */
    protected $alias;

    /** @var array $join */
    protected $join;

    /** @var array $union */
    protected $union;

    /** @var array $binds */
    protected $binds;

    /** @var string $query */
    protected $query;

    /**
     * QueryBuilder constructor.
     * @param string $table
     * @param array $columns
     * @param array $options
     * @param array $orderBy
     * @param array $limit
     * @throws OperationFailedException
     * Example:
     * new QueryBuilder(
     *      'table_name',
     *      ['column1', 'column2', 'column3'],
     *      [
     *          [
     *              'CONDITIONS' => [
     *                    'column1' => ['=' => false],
     *                    'column2' => ['BETWEEN', ['2018-12-01', '2018-12-06']],
     *                    'column3' => ['IN' => ['user_1_id', 'user_2_id']]
     *          ],
     *          [
     *              'UNION' => [
     *                  'type' => 'ALL' (optional),
     *                  'table' => 'table_name',
     *                  'alias' => 't1',
     *                  'columnsAlias' => 't2'
     *              ]
     *          ],
     *          [
     *              'JOIN' => [
     *                   'table' => 'table_name',
     *                   'alias' => 't2',
     *                   'side' => 'INNER|OUTER|LEFT|RIGHT|FULL OUTER',
     *                   'conditions' => [
     *                        't2.column1' => ['=' => 't1.column1', 'process' => false]
     *                   ]
     *              ]
     *          ]
     *      ],
     *      [
     *          'column' => 'order_by_column',
     *          'direction' => 'ASC|DESC'
     *      ],
     *      [
     *          'limit' => 1,
     *          'offset' => 1
     *      ]
     * );
     */
    public function __construct(string $table, array $columns, array $options = [], array $orderBy = [], array $limit = [])
    {
        if (empty($table)) {
            throw new OperationFailedException('Table should be specified', 500);
        }
        $this->table = $table;
        $this->columns = $columns;
        $this->options = $options;
        $this->orderBy = $orderBy;
        $this->limit = $limit;
        $this->createQuery();
    }

    /**
     * @param array $values
     * @return array
     */
    protected final function bindValuesAliases(array $values)
    {
        $bindValues = [];
        $randomString = strtoupper(substr(str_shuffle('abcdefghijklmnopqrstuvwxyz'), 0, 3));
        for ($i = 0; $i < count($values); $i++) {
            $this->binds[$randomString.$i] = $values[$i];
            $bindValues[] = ':'.$randomString.$i;
        }
        return $bindValues;
    }

    /**
     * @return string
     */
    public function getQuery(): string
    {
        return $this->query;
    }

    /**
     * @return array|null
     */
    public function getBinds(): ?array
    {
        return $this->binds;
    }

    /**
     * @return string
     */
    protected function getAlias()
    {
        return $this->alias ?: $this->alias = substr($this->table, 0, 1) . rand(0, 9);
    }

    /**
     * @param array $columns
     * @param string $alias
     * @return array
     */
    public static function columnsAlias(array $columns, string $alias)
    {
        return array_map(function($column) use($alias) {return $alias.'.'.$column;}, $columns);
    }

    /**
     * @param string $alias
     * @return string
     */
    protected function getColumns($alias = ''): string
    {
        $alias = $alias ?: $this->getAlias();
        if (!is_array($this->columns) || empty($this->columns)) {
            return $alias.'.*';
        }
        $columns = array_map(function ($column) use ($alias) {
            if (strpos($column, '.') !== false) {
                return $column;
            }
            return $alias.'.'.$column;
        }, $this->columns);
        return implode(',', $columns);
    }

    /**
     * Generate query join statement
     * @return string
     * @throws OperationFailedException
     */
    protected final function createJoin(): string
    {
        $joinStatement = '';
        if (is_array($this->join)) {
            if (empty($this->join['table'])) {
                throw new OperationFailedException('Check that all join attributes are set', 500);
            }

            if (!empty($this->join['side']) && !in_array(strtoupper($this->join['side']), ['LEFT', 'RIGHT', 'OUTER', 'INNER', 'FULL OUTER'])) {
                unset($this->join['side']);
            }

            $this->table = $this->join['table'];

            $tableAlias = $this->join['alias'] ?: $this->getAlias();

            $side = '';
            if (array_key_exists('side', $this->join)) {
                $side = $this->join['side'];
            }

            $joinStatement .= "\n" . $side . ' JOIN ' . $this->join['table'] . ' AS ' . $tableAlias;

            if (!empty($this->join['conditions'])) {
                $this->conditions = $this->join['conditions'];
                $joinStatement .= ' ON ' . $this->conditionsBuilder();
            }
        }
        return $joinStatement;
    }

    /**
     * @return string
     * @throws OperationFailedException
     */
    protected final function createUnion(): string
    {
        if (!is_array($this->union) || empty($this->union)) {
            throw new OperationFailedException('You have to specify UNION options', 500);
        }
        $unionStatement = '';
        $this->options = [];
        if (empty($this->union['table']))  {
            throw new OperationFailedException('Not allowed UNION without table name', 500);
        }
        $this->orderBy = $this->limit = [];
        $this->table = $this->union['table'];
        $this->columns = self::columnsAlias($this->columns, $this->union['columnsAlias'] ?? $this->union['alias'] ?? $this->alias);
        $this->alias = $this->union['alias'];
        $unionStatement .= "\n" . 'UNION ' .
            (array_key_exists('type', $this->union) ? $this->union['type'] : '')
                . ' ' . self::createQuery(false);
        $this->options = [];
        return $unionStatement;
    }

    /**
     * @return string
     */
    protected final function conditionsBuilder()
    {
        $conditions = '';
        if (!empty($this->conditions) && is_array($this->conditions)) {
            $columns = $this->columns;
            $conditions .= implode(' AND ',
                    array_map(function ($column, $value) use($columns) {
                        if (array_key_exists($column, $columns)) {
                            $column = $columns[$column];
                        }
                        $operator = strtoupper(key($value));
                        if (array_key_exists('process', $value) && $value['process'] === false) {
                            return $column . ' ' . $operator . ' ' . $value[$operator];
                        }
                        if ($alias = strpos($column, '.') === false) {
                            $alias = ($this->alias ?: $this->table). '.';
                        }
                        switch ($operator) {
                            case SchemaQueryOperatorsEnum::OP_EQUALS:
                            case SchemaQueryOperatorsEnum::OP_NOT_EQUAL:
                            case SchemaQueryOperatorsEnum::OP_GREATER_THAN:
                            case SchemaQueryOperatorsEnum::OP_GREATER_THAN_EQUAL:
                            case SchemaQueryOperatorsEnum::OP_LESS_THAN:
                            case SchemaQueryOperatorsEnum::OP_LESS_THAN_EQUAL:
                                $condition = $alias . $column . ' ' . $operator . ' ' . implode(self::bindValuesAliases([$value[$operator]]));
                                break;
                            case SchemaQueryOperatorsEnum::OP_IN:
                            case SchemaQueryOperatorsEnum::OP_NOT_IN:
                                if (!is_array($value[$operator])) {
                                    throw new \Exception('Invalid value type for IN', 500);
                                }
                                $condition = $alias . $column . ' ' .$operator . ' ('.implode(',', self::bindValuesAliases($value[$operator])).')';
                                break;
                            case SchemaQueryOperatorsEnum::OP_BETWEEN:
                                if (!is_array($value[$operator]) || count($value[$operator]) < 2) {
                                    throw new \Exception('Invalid value type for BETWEEN', 500);
                                }
                                $bindValues = self::bindValuesAliases($value[$operator]);
                                $condition = $alias . $column . ' ' . $operator . $bindValues[0] . ' AND ' . $bindValues[1];
                                break;
                            case SchemaQueryOperatorsEnum::OP_IS_NULL:
                            case SchemaQueryOperatorsEnum::OP_IS_NOT_NULL:
                                $condition = $alias . $column . ' ' . $operator;
                                break;
                            default:
                                throw new \Exception('Unknown operator: ' . $operator, 500);
                        }
                        return $condition;
                    }, array_keys($this->conditions), $this->conditions)
                );
        }
        return $conditions;
    }

    /**
     * Create query
     * @param bool $endStatement
     * @return string
     * @throws OperationFailedException
     */
    protected function createQuery(bool $endStatement = true)
    {
        $alias = $this->getAlias();
        $query = 'SELECT ' . self::getColumns($alias) . ' FROM ' . $this->table. ' AS ' . $alias;

        // To prevent override reference values
        $options = $this->options;
        $orderBy = $this->orderBy;
        $limit = $this->limit;

        foreach ($options as $option) {
            $key = strtoupper(key($option));
            $option = array_shift($option);
            if ($key == 'JOIN') {
                $this->join = $option;
                $query .= $this->createJoin();
            } elseif ($key == 'UNION') {
                $this->union = $option;
                $query .= $this->createUnion();
            } elseif ($key == 'CONDITIONS') {
                $this->conditions = $option;
                $query .= "\n" . ' WHERE ' . $this->conditionsBuilder();
            } else {
                continue;
            }
        }

        if ($orderBy && !empty($orderBy['column'])) {
            $query .= ' ORDER BY ' . $alias . '.' . $orderBy['column'] . ' ' . ($orderBy['direction'] ?: 'ASC');
        }

        if ($limit) {
            $query .= ' LIMIT ' . $limit['limit'];
            if ($limit['offset']) {
                $query .= ' OFFSET ' . $limit['offset'];
            }
        }

        $query = ($endStatement) ? $query.';' : $query;
        return $this->query = $query;
    }
}