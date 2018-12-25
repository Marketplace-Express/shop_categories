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
    private $statements;
    
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
                            'size' => 100,
                            'after' => 'category_parent_id'
                        ]
                    ),
                    new Column(
                        'category_order',
                        [
                            'type' => Column::TYPE_INTEGER,
                            'default' => "0",
                            'notNull' => true,
                            'size' => 3,
                            'after' => 'category_name'
                        ]
                    ),
                    new Column(
                        'category_vendor_id',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 36,
                            'after' => 'category_order'
                        ]
                    ),
                    new Column(
                        'category_user_id',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'notNull' => true,
                            'size' => 36,
                            'after' => 'category_vendor_id'
                        ]
                    ),
                    new Column(
                        'created_at',
                        [
                            'type' => Column::TYPE_DATETIME,
                            'notNull' => true,
                            'size' => 1,
                            'after' => 'category_user_id'
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
                    new Index('category_id_uindex', ['category_id'], 'UNIQUE'),
                    new Index('category_parent_id_index', ['category_parent_id'], null)
                ],
                'references' => [
                    new Reference(
                        'category_ids_fk',
                        [
                            'referencedTable' => 'category',
                            'referencedSchema' => 'shop_categories',
                            'columns' => ['category_parent_id'],
                            'referencedColumns' => ['category_id'],
                            'onUpdate' => 'NO ACTION',
                            'onDelete' => 'NO ACTION'
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

    public function afterCreateTable()
    {
        // create insert log trigger
        $this->statements[] = "DROP TRIGGER IF EXISTS trigger_log_insert;
                CREATE TRIGGER trigger_log_insert
                  AFTER INSERT
                  ON category
                  FOR EACH ROW
                BEGIN
                    INSERT INTO admins_logs (action, user_id, category_id, action_data, done_at)
                    VALUES
                         ('INSERT',
                          new.category_user_id,
                          new.category_id,
                          json_object(
                            'category_name', new.category_name,
                            'category_parent_id', new.category_parent_id,
                            'category_order', new.category_order,
                            'category_vendor_id', new.category_vendor_id
                            ),
                          now());
                END;";

        // create update log trigger
        $this->statements[] = "DROP TRIGGER IF EXISTS trigger_log_update;
                CREATE TRIGGER trigger_log_update
                  AFTER UPDATE
                  ON category
                  FOR EACH ROW
                BEGIN
                    INSERT INTO admins_logs (action, user_id, category_id, action_data, done_at)
                    VALUES
                    ('UPDATE',
                    new.category_user_id,
                    new.category_id,
                    json_object(
                       'old_category_name', old.category_name,
                       'old_category_parent_id', old.category_parent_id,
                       'category_name', new.category_name,
                       'category_parent_id', new.category_parent_id,
                       'category_order', new.category_order,
                       'category_vendor_id', new.category_vendor_id,
                       'is_deleted', new.is_deleted,
                       'deleted_at', new.deleted_at
                     ),
                    now());
                END;";

        return $this->executeStatements();
    }

    public function executeStatements()
    {
        foreach ($this->statements as $statement) {
            $this->getConnection()->query($statement);
        }
        return true;
    }

}
