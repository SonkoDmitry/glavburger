<?php

use yii\db\Schema;
use yii\db\Migration;

class m150719_160834_add_results_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('results', [
            'id' => Schema::TYPE_PK,
            'user_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'location' => 'geometry NOT NULL',
            'longitude' => Schema::TYPE_DOUBLE . ' NOT NULL',
            'latitude' => Schema::TYPE_DOUBLE . ' NOT NULL',
            'point' => 'point NOT NULL',
            'offset' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0',
            'total' => Schema::TYPE_INTEGER . ' NOT NULL',
            'created_at' => Schema::TYPE_DATETIME . ' NOT NULL',
            'updated_at' => Schema::TYPE_DATETIME . ' NOT NULL',
        ]);
        $this->addForeignKey('result_to_user', 'results', 'user_id', 'users', 'id', 'CASCADE');
    }

    public function safeDown()
    {
        $this->dropTable('results');
    }
}
