<?php
/**
 * User: Wajdi Jurry
 * Date: 20/11/18
 * Time: 02:01 م
 */

namespace Shop_categories\Test\Models\Behaviors;

use Phalcon\Db\Adapter\Pdo\Mysql;
use Phalcon\Db\AdapterInterface;
use Phalcon\Db\Result\Pdo;
use Phalcon\Db\ResultInterface;
use Phalcon\Di\FactoryDefault;
use Phalcon\Mvc\Model\Manager;
use Phalcon\Mvc\Model\MetaData\Memory;
use PHPUnit\Framework\MockObject\MockObject;
use Shop_categories\Models\Behaviors\AdjacencyListModelBehavior;
use Shop_categories\Models\Category;

class AdjacencyListModelBehaviorTest extends \UnitTestCase
{
    const ITEM_ID = '4b157f1b-8134-4688-8e91-5bdcbe342757';
    public $params = [
        'itemIdAttribute' => 'itemId',
        'parentIdAttribute' => 'parentId',
        'orderByAttribute' => 'itemOrder',
        'isDeletedAttribute' => 'isDeleted',
        'isDeletedValue' => true,
        'subItemsSlug' => 'children',
        'noParentValue' => null
    ];

    public function getModelMock(...$methods)
    {
        return $this->getMockBuilder(Category::class)
            ->disableOriginalConstructor()
            ->setMethods($methods)
            ->getMock();
    }

    public function getBehaviorMock(...$methods)
    {
        return $this->getMockBuilder(AdjacencyListModelBehavior::class)
            ->setConstructorArgs([$this->params])
            ->setMethods($methods)
            ->getMock();
    }

    public function servicesProvider(array $services = [])
    {
        $di = new FactoryDefault();
        $di->setShared(
            'db',
            $this->getMockBuilder(Mysql::class)->disableOriginalConstructor()->getMock()
        );
        foreach ($services as $service) {
            $di->set(
                $service['name'],
                $this->getMockBuilder($service['class'])->setMethods($service['methods'] ?? [])->getMock(),
                $service['shared'] ?? false
            );
        }
        return $di;
    }

    /**
     * @throws \Exception
     */
    public function testCascadeDelete()
    {
        $di = $this->servicesProvider();

        /** @var Category|MockObject $sampleModel */
        $sampleModel = $this->getModelMock('delete');
        $sampleModel->setDI($di);
        $sampleModel->expects(self::atLeastOnce())->method('delete')->willReturn(true);

        /** @var AdjacencyListModelBehavior|MockObject $behaviorMock */
        $behaviorMock = $this->getBehaviorMock('descendants');
        $behaviorMock->expects(self::once())->method('descendants')->with(self::ITEM_ID, false)->willReturn([$sampleModel]);

        $behaviorMock->missingMethod($sampleModel, 'cascadeDelete', [self::ITEM_ID]);
    }

    /**
     * @throws \Exception
     */
    public function testCascadeDeleteWithException()
    {
        $di = $this->servicesProvider();

        /** @var Category|MockObject $sampleModel */
        $sampleModel = $this->getModelMock('delete', 'getItemId');
        $sampleModel->setDI($di);
        $sampleModel->expects(self::once())->method('getItemId')->willReturn(self::ITEM_ID);
        $sampleModel->expects(self::atLeastOnce())->method('delete')->willReturn(false);

        /** @var AdjacencyListModelBehavior|MockObject $behaviorMock */
        $behaviorMock = $this->getBehaviorMock('descendants');
        $behaviorMock->expects(self::once())->method('descendants')->with(self::ITEM_ID, false)->willReturn([$sampleModel]);

        $this->expectExceptionMessage("Item ".self::ITEM_ID." could not be deleted");

        $behaviorMock->missingMethod($sampleModel, 'cascadeDelete', [self::ITEM_ID]);
    }

    /**
     * @throws \Exception
     */
    public function testChildren()
    {
        $di = $this->servicesProvider([
            [
                'name' => 'modelsManager',
                'class' => Manager::class,
                'methods' => ['find'],
                'shared' => false
            ]
        ]);

        /** @var Category|MockObject $modelMock */
        $modelMock = $this->getModelMock('nothing');
        $modelMock->setDI($di);

        /** @var AdjacencyListModelBehavior|MockObject $behaviorMock */
        $behaviorMock = $this->getBehaviorMock('find');
        $behaviorMock->expects(self::once())->method('find')->with(
            'parentId = :parentId: AND isDeleted <> :isDeletedValue:',
            ['parentId' => self::ITEM_ID, 'isDeletedValue' => true]
        )->willReturn(['some result']);

        $behaviorMock->missingMethod($modelMock, 'children', [self::ITEM_ID, false]);
    }

    /**
     * @throws \Exception
     */
    public function testChildrenWithException()
    {
        $di = $this->servicesProvider();

        /** @var Category|MockObject $modelMock */
        $modelMock = $this->getModelMock('nothing');
        $modelMock->setDI($di);

        /** @var AdjacencyListModelBehavior|MockObject $behaviorMock */
        $behaviorMock = $this->getBehaviorMock('find');
        $behaviorMock->expects(self::once())->method('find')->with(
            'parentId = :parentId: AND isDeleted <> :isDeletedValue:',
            ['parentId' => self::ITEM_ID, 'isDeletedValue' => true]
        )->willReturn([]);

        $this->expectExceptionMessage('Item not found or maybe deleted');
        $this->expectExceptionCode(404);

        $behaviorMock->missingMethod($modelMock, 'children', [self::ITEM_ID, false]);
    }

    /**
     * @throws \Exception
     */
    public function testIsDescendant()
    {
        $sampleItems = [
            ['itemId' => self::ITEM_ID, 'parentId' => '5533ce37-2693-4abc-9209-e9c21cb6ac17'],
            ['itemId' => 'b91b11e8-909a-4691-b550-0bf5fae29e66', 'parentId' => self::ITEM_ID],
            ['itemId' => '380a92c3-f8bb-4980-845a-d61e7a403355', 'parentId' => 'b91b11e8-909a-4691-b550-0bf5fae29e66']
        ];

        /** @var AdjacencyListModelBehavior|MockObject $behaviorMock */
        $behaviorMock = $this->getBehaviorMock('descendants');
        $behaviorMock->expects(self::any())->method('descendants')->with(self::ITEM_ID, true, false)->willReturn($sampleItems);

        $this->assertFalse($behaviorMock->isDescendant(self::ITEM_ID, 'c7e26cfb-2c52-40f6-ba4e-56b5c2ca5d12'));
        $this->assertTrue($behaviorMock->isDescendant(self::ITEM_ID, 'b91b11e8-909a-4691-b550-0bf5fae29e66'));
    }

    /**
     * @throws \Exception
     */
    public function testRoots()
    {
        $di = $this->servicesProvider();

        /** @var Category|MockObject $modelMock */
        $modelMock = $this->getModelMock('nothing');
        $modelMock->setDI($di);

        /** @var AdjacencyListModelBehavior|MockObject $behaviorMock */
        $behaviorMock = $this->getBehaviorMock('find');
        $behaviorMock->expects(self::once())->method('find')->with(
            'parentId = :noParentValue: AND isDeleted <> :isDeletedValue:',
            ['noParentValue' => null, 'isDeletedValue' => true]
        )->willReturn(['some result']);

        $behaviorMock->missingMethod($modelMock, 'roots', [false]);
    }

    /**
     * @throws \Exception
     */
    public function testRootsWithException()
    {
        $di = $this->servicesProvider();

        /** @var Category|MockObject $modelMock */
        $modelMock = $this->getModelMock('nothing');
        $modelMock->setDI($di);

        /** @var AdjacencyListModelBehavior|MockObject $behaviorMock */
        $behaviorMock = $this->getBehaviorMock('find');
        $behaviorMock->expects(self::once())->method('find')->with(
            'parentId = :noParentValue: AND isDeleted <> :isDeletedValue:',
            ['noParentValue' => null, 'isDeletedValue' => true]
        )->willReturn([]);

        $this->expectExceptionMessage('No roots found');
        $this->expectExceptionCode(404);

        $behaviorMock->missingMethod($modelMock, 'roots', [false]);
    }

    /**
     * @throws \Exception
     */
    public function testParents()
    {
        /** @var Category|MockObject $modelMock */
        $modelMock = $this->getModelMock('getSource', 'getReadConnection', 'columnMap');

        /** @var ResultInterface|MockObject $simpleResultMock */
        $simpleResultMock = $this->getMockBuilder(\Phalcon\Mvc\Model\Resultset\Simple::class)
            ->disableOriginalConstructor()
            ->setMethods(['setFetchMode', 'fetchAll'])
            ->getMock();
        $simpleResultMock->expects(self::once())->method('setFetchMode')->with(\PDO::FETCH_CLASS, get_class($modelMock));
        $simpleResultMock->expects(self::once())->method('fetchAll')->willReturn(['some result']);

        /** @var AdapterInterface|MockObject $pdoAdapterMock */
        $pdoAdapterMock = $this->getMockBuilder(Pdo::class)
            ->disableOriginalConstructor()
            ->setMethods(['query'])
            ->getMock();
        $pdoAdapterMock->expects(self::once())->method('query')->withAnyParameters()->willReturn($simpleResultMock);

        $di = $this->servicesProvider([
            [
                'name' => 'modelsMetadata',
                'class' => Memory::class,
                'methods' => ['readColumnMap'],
                'shared' => false
            ]
        ]);

        $modelMock->expects(self::once())->method('getSource')->willReturn('shop_categories');
        $modelMock->expects(self::once())->method('getReadConnection')->willReturn($pdoAdapterMock);
        $modelMock->expects(self::any())->method('columnMap')->willReturn([
                'item_id' => 'itemId',
                'parent_id' => 'parentId',
                'is_deleted' => 'isDeleted',
                'item_order' => 'itemOrder'
        ]);
        $modelMock->setDI($di);

        /** @var AdjacencyListModelBehavior|MockObject $behaviorMock */
        $behaviorMock = $this->getBehaviorMock('nothing');

        $behaviorMock->missingMethod($modelMock, 'parents', [self::ITEM_ID, false]);
    }

    /**
     * @throws \Exception
     */
    public function testDescendants()
    {
        /** @var Category|MockObject $modelMock */
        $modelMock = $this->getModelMock('getSource', 'getReadConnection', 'columnMap');

        /** @var ResultInterface|MockObject $simpleResultMock */
        $simpleResultMock = $this->getMockBuilder(\Phalcon\Mvc\Model\Resultset\Simple::class)
            ->disableOriginalConstructor()
            ->setMethods(['setFetchMode', 'fetchAll'])
            ->getMock();
        $simpleResultMock->expects(self::once())->method('setFetchMode')->with(\PDO::FETCH_CLASS, get_class($modelMock));
        $simpleResultMock->expects(self::once())->method('fetchAll')->willReturn(['some result']);

        /** @var AdapterInterface|MockObject $pdoAdapterMock */
        $pdoAdapterMock = $this->getMockBuilder(Pdo::class)
            ->disableOriginalConstructor()
            ->setMethods(['query'])
            ->getMock();
        $pdoAdapterMock->expects(self::once())->method('query')->withAnyParameters()->willReturn($simpleResultMock);

        $di = $this->servicesProvider([
            [
                'name' => 'modelsMetadata',
                'class' => Memory::class,
                'methods' => ['readColumnMap'],
                'shared' => false
            ]
        ]);

        $modelMock->expects(self::once())->method('getSource')->willReturn('shop_categories');
        $modelMock->expects(self::once())->method('getReadConnection')->willReturn($pdoAdapterMock);
        $modelMock->expects(self::any())->method('columnMap')->willReturn([
            'item_id' => 'itemId',
            'parent_id' => 'parentId',
            'is_deleted' => 'isDeleted',
            'item_order' => 'itemOrder'
        ]);
        $modelMock->setDI($di);

        /** @var AdjacencyListModelBehavior|MockObject $behaviorMock */
        $behaviorMock = $this->getBehaviorMock('nothing');

        $behaviorMock->missingMethod($modelMock, 'descendants', [self::ITEM_ID, false]);
    }
}
