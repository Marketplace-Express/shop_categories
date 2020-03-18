<?php
/**
 * User: Wajdi Jurry
 * Date: 10/12/18
 * Time: 12:24 ص
 */

namespace tests\app\common\dbTools;

use app\common\dbTools\enums\SchemaQueryOperatorsEnum;
use app\common\dbTools\RecursiveQueryBuilder;

class RecursiveQueryBuilderTest extends \UnitTestCase
{
    const TABLE_NAME = 'test';
    const ANOTHER_TABLE_NAME = 'test2';
    const COLUMNS = ['column1', 'column2'];

    /** @var \PDO $pdo */
    public $pdo;

    public function setUp()
    {
        parent::setUp();
        $config = $this->di->getConfig();
        $this->pdo = new \PDO(sprintf('mysql:dbname=%s;host=%s:%d', $_ENV['TEST_DB_NAME'], $config->database->host, $config->database->port), $config->database->username, $config->database->password, [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_TIMEOUT => 10
        ]);
        $this->createTables();
    }

    /**
     * @param $tableName
     * @param $columns
     * @param string $recursionName
     * @param array $options
     * @param array $orderBy
     * @param array $limit
     * @param mixed ...$methods
     * @return \PHPUnit\Framework\MockObject\MockObject|RecursiveQueryBuilder
     */
    public function getQueryBuilderMock($tableName, $columns, string $recursionName, $options = [], $orderBy = [], $limit = [], ...$methods)
    {
        return $this->getMockBuilder(RecursiveQueryBuilder::class)
            ->setConstructorArgs([$tableName, $columns, $recursionName, $options, $orderBy, $limit])
            ->setMethods($methods)
            ->getMock();
    }

    public function testCreateQuery()
    {
        $queryBuilder = new RecursiveQueryBuilder(
            'test',
            ['column1', 'column2'],
            'category_recursive_query',
            [
                [
                    'CONDITIONS' => [
                        'column1' => [SchemaQueryOperatorsEnum::OP_LESS_THAN_EQUAL => '100'],
                        'column2' => [SchemaQueryOperatorsEnum::OP_EQUALS => false]
                    ]
                ],
                [
                    'UNION' => [
                        'type' => 'ALL',
                        'table' => 'test',
                        'alias' => 't0',
                        //'columnsAlias' => 't1'
                    ]
                ],
                [
                    'JOIN' => [
                        'table' => 'test',
                        'alias' => 't1',
                        'conditions' => [
                            't1.column1' => [SchemaQueryOperatorsEnum::OP_EQUALS => 't0.column1', 'process' => false],
                            't1.column2' => [SchemaQueryOperatorsEnum::OP_EQUALS => false]
                        ]
                    ]
                ]
            ],
            [
                'column' => 'column1',
                'direction' => 'ASC'
            ],
            [
                'limit' => 10,
                'offset' => 1
            ]
        );

        $pdo = $this->pdo->prepare($queryBuilder->getQuery());
        foreach ($queryBuilder->getBinds() as $param => $value) {
            $pdo->bindParam($param, $value);
        }
        // check if query is valid
        $pdo->execute();

        $this->assertCount(3, $queryBuilder->getBinds());
    }
}
