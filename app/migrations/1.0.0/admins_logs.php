<?php 

use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Db\Reference;
use Phalcon\Mvc\Model\Migration;

/**
 * Class AdminsLogsMigration_100
 */
class AdminsLogsMigration_100 extends Migration
{
    /**
     * Define the table structure
     *
     * @return void
     */
    public function morph()
    {
        $this->morphTable('admins_logs', [
                'columns' => [
                    new Column(
                        'action',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 6,
                            'first' => true
                        ]
                    ),
                    new Column(
                        'user_id',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 36,
                            'after' => 'action'
                        ]
                    ),
                    new Column(
                        'category_id',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 36,
                            'after' => 'user_id'
                        ]
                    ),
                    new Column(
                        'action_data',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 1,
                            'after' => 'category_id'
                        ]
                    ),
                    new Column(
                        'done_at',
                        [
                            'type' => Column::TYPE_DATETIME,
                            'default' => "CURRENT_TIMESTAMP",
                            'size' => 1,
                            'after' => 'action_data'
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
        // Phalcon 3.4.x does not support json column type
        return $this->getConnection()->query('alter table admins_logs modify action_data json null;');
    }

}
