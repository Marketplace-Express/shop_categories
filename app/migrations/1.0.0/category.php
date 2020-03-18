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
     */
    public function morph()
    {
        $this->morphTable('category', [
                'columns' => [
                    new Column(
                        'id',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'notNull' => true,
                            'size' => 36,
                            'first' => true
                        ]
                    ),
                    new Column(
                        'parent_id',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 36,
                            'after' => 'id'
                        ]
                    ),
                    new Column(
                        'name',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'notNull' => true,
                            'size' => 100,
                            'after' => 'parent_id'
                        ]
                    ),
                    new Column(
                        'order',
                        [
                            'type' => Column::TYPE_INTEGER,
                            'default' => "0",
                            'notNull' => true,
                            'size' => 3,
                            'after' => 'name'
                        ]
                    ),
                    new Column(
                        'vendor_id',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'notNull' => true,
                            'size' => 36,
                            'after' => 'order'
                        ]
                    ),
                    new Column(
                        'user_id',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'notNull' => true,
                            'size' => 36,
                            'after' => 'vendor_id'
                        ]
                    ),
                    new Column(
                        'url',
                        [
                            'type' => Column::TYPE_TEXT,
                            'size' => 1,
                            'after' => 'user_id'
                        ]
                    ),
                    new Column(
                        'depth',
                        [
                            'type' => Column::TYPE_INTEGER,
                            'default' => "0",
                            'size' => 2,
                            'after' => 'url'
                        ]
                    ),
                    new Column(
                        'created_at',
                        [
                            'type' => Column::TYPE_DATETIME,
                            'default' => "CURRENT_TIMESTAMP",
                            'size' => 1,
                            'after' => 'depth'
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
                            'notNull' => true,
                            'size' => 1,
                            'after' => 'deleted_at'
                        ]
                    )
                ],
                'indexes' => [
                    new Index('PRIMARY', ['id'], 'PRIMARY'),
                    new Index('category_id-category_parent_id_unique', ['id', 'parent_id'], 'UNIQUE'),
                    new Index('category_parent_id_index', ['parent_id'])
                ],
                'references' => [
                    new Reference(
                        'category_ids_fk',
                        [
                            'referencedTable' => 'category',
                            'referencedSchema' => 'shop_categories',
                            'columns' => ['parent_id'],
                            'referencedColumns' => ['id'],
                            'onUpdate' => 'NO ACTION',
                            'onDelete' => 'CASCADE'
                        ]
                    )
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
