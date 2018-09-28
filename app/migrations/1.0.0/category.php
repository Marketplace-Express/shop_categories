<?php 

use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Db\Reference;
use Phalcon\Mvc\Model\Migration;

/**
 * Class CategoryMigration_100
 */
class CategoryMigration_100 extends Migration
{
    /**
     * Define the table structure
     *
     * @return void
     * @throws \Phalcon\Db\Exception
     */
    public function morph()
    {
        $this->morphTable('category', [
                'columns' => [
                    new Column(
                        'category_id',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'notNull' => true,
                            'size' => 36,
                            'first' => true
                        ]
                    ),
                    new Column(
                        'category_parent_id',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 36,
                            'after' => 'category_id'
                        ]
                    ),
                    new Column(
                        'category_name',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'notNull' => true,
                            'size' => 255,
                            'after' => 'category_parent_id'
                        ]
                    ),
                    new Column(
                        'category_order',
                        [
                            'type' => Column::TYPE_INTEGER,
                            'notNull' => true,
                            'default' => "0",
                            'size' => 3,
                            'after' => 'category_name'
                        ]
                    ),
                    new Column(
                        'vendor_id',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 36,
                            'after' => 'category_name'
                        ]
                    ),
                    new Column(
                        'created_at',
                        [
                            'type' => Column::TYPE_DATETIME,
                            'notNull' => true,
                            'size' => 1,
                            'after' => 'vendor_id'
                        ]
                    ),
                    new Column(
                        'updated_at',
                        [
                            'type' => Column::TYPE_DATETIME,
                            'size' => 1,
                            'after' => 'created_at'
                        ]
                    ),
                    new Column(
                        'deleted_at',
                        [
                            'type' => Column::TYPE_DATETIME,
                            'size' => 1,
                            'after' => 'updated_at'
                        ]
                    ),
                    new Column(
                        'is_deleted',
                        [
                            'type' => Column::TYPE_INTEGER,
                            'default' => "0",
                            'size' => 1,
                            'after' => 'deleted_at'
                        ]
                    )
                ],
                'indexes' => [
                    new Index('PRIMARY', ['category_id'], 'PRIMARY'),
                    new Index('category_category_id_uindex', ['category_id'], 'UNIQUE'),
                    new Index('category__parent_id', ['category_parent_id'], null)
                ],
                'options' => [
                    'TABLE_TYPE' => 'BASE TABLE',
                    'AUTO_INCREMENT' => '',
                    'ENGINE' => 'InnoDB',
                    'TABLE_COLLATION' => 'utf8mb4_0900_ai_ci'
                ],
            ]
        );
    }

    /**
     * Run the migrations
     *
     * @return void
     */
    public function up()
    {

    }

    /**
     * Reverse the migrations
     *
     * @return void
     */
    public function down()
    {

    }

}
