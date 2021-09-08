<?php

use yii\db\Schema;
use yii\db\Migration;

class m150716_194817_add_feedback_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('feedback', [
            'id' => Schema::TYPE_PK,
            'user_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'text' => Schema::TYPE_TEXT . ' NOT NULL',
            'created_at' => Schema::TYPE_DATETIME . ' NOT NULL',
            'updated_at' => Schema::TYPE_DATETIME . ' NOT NULL',
        ]);
        $this->addForeignKey('feedback_to_user', 'feedback', 'user_id', 'users', 'id', 'CASCADE');
    }

    public function safeDown()
    {
        $this->dropTable('feedback');
    }
}
