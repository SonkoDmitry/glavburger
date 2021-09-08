<?php

use yii\db\Schema;
use yii\db\Migration;

class m150716_182614_add_messages_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('messages', [
            'id' => Schema::TYPE_PK,
            'user_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'telegram_message_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'telegram_update_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'type' => Schema::TYPE_STRING . ' NOT NULL',
            'content' => Schema::TYPE_TEXT . ' DEFAULT NULL',
            'raw' => Schema::TYPE_TEXT . ' NOT NULL',
            'created_at' => Schema::TYPE_DATETIME . ' NOT NULL',
            'updated_at' => Schema::TYPE_DATETIME . ' NOT NULL',
        ]);
        $this->addForeignKey('messages_to_user', 'messages', 'user_id', 'users', 'id', 'CASCADE');
    }

    public function safeDown()
    {
        $this->dropTable('messages');
    }
}
