<?php

use yii\db\Schema;
use yii\db\Migration;

class m150721_123625_add_redirects_log_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('redirects_log', [
            'id' => Schema::TYPE_PK,
            'redirect_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'ip' => Schema::TYPE_STRING . ' DEFAULT NULL',
            'user_agent' => Schema::TYPE_TEXT . ' DEFAULT NULL',
            'headers' => Schema::TYPE_TEXT . ' DEFAULT NULL',
            'created_at' => Schema::TYPE_DATETIME . ' NOT NULL',
            'updated_at' => Schema::TYPE_DATETIME . ' NOT NULL',
        ]);
        $this->addForeignKey('redirects_log_to_redirects', 'redirects_log', 'redirect_id', 'redirects', 'id', 'CASCADE');
    }

    public function safeDown()
    {
        $this->dropTable('redirects_log');
    }
}
