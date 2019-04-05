<?php
/**
 * User: Wajdi Jurry
 * Date: 09/12/18
 * Time: 11:07 م
 */

namespace Shop_categories\Tests\DBTools;

use Shop_categories\DBTools\Enums\SchemaQueryOperatorsEnum;
use Shop_categories\DBTools\QueryBuilder;
use PDO;

class QueryBuilderTest extends \UnitTestCase
{
    const TABLE_NAME = 'test';
    const ANOTHER_TABLE_NAME = 'test2';
    const COLUMNS = ['column1', 'column2'];

    /** @var PDO $pdo */
    public $pdo;

    public function setUp()
    {
        parent::setUp();
        if (!extension_loaded('pdo') || !extension_loaded('pdo_mysql')) {
            $this->markTestSkipped('PDO extension should be enabled');
        }
        $this->pdo = new PDO('mysql:dbname=test;host=127.0.0.1:3306', 'test', 'test', [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_TIMEOUT =>  10
        ]);
    }

    /**
     * @param $tableName
     * @param array $columns
     * @param array $options
     * @param array $orderBy
     * @param array $limit
     * @param mixed ...$methods
     * @return \PHPUnit\Framework\MockObject\MockObject|QueryBuilder
     */
    public function getQueryBuilderMock($tableName, $columns, $options = [], $orderBy = [], $limit = [], ...$methods)
    {
        return $this->getMockBuilder(QueryBuilder::class)
            ->setConstructorArgs([$tableName, $columns, $options, $orderBy, $limit])
            ->setMethods($methods)
            ->getMock();
    }

    public function testColumnsAlias()
    {
        $this->assertEquals(['c.column1', 'c.column2'], QueryBuilder::columnsAlias(self::COLUMNS, 'c'));
    }

    public function testCreateQuery()
    {
        $queryBuilder = new QueryBuilder(self::TABLE_NAME, self::COLUMNS,
            [
                [
                    'CONDITIONS' => [
                        'column1' => [SchemaQueryOperatorsEnum::OP_EQUALS => false],
                        'column2' => [SchemaQueryOperatorsEnum::OP_NOT_IN => [1,2,3]]
                    ]
                ]
            ],
            [
                'column' => 'column2',
                'direction' => 'ASC'
            ],
            [
                'limit' => 3,
                'offset' => 1
            ]
        );

        $pdo = $this->pdo->prepare($queryBuilder->getQuery());
        foreach ($queryBuilder->getBinds() as $param => $value) {
            $pdo->bindParam($param, $value);
        }
        // check if query is valid
        $pdo->execute();

        $this->assertCount(4, $queryBuilder->getBinds());
    }
}
