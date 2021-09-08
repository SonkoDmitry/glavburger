<?php

use yii\db\Schema;
use yii\db\Migration;

class m150721_123617_add_redirects_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('redirects', [
            'id' => Schema::TYPE_PK,
            'place_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'code' => Schema::TYPE_STRING . ' NOT NULL',
            'url' => Schema::TYPE_STRING . ' NOT NULL',
            'counter' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0',
            'created_at' => Schema::TYPE_DATETIME . ' NOT NULL',
            'updated_at' => Schema::TYPE_DATETIME . ' NOT NULL',
        ]);
        $this->addForeignKey('redirects_to_places', 'redirects', 'place_id', 'places', 'id', 'CASCADE');
    }

    public function safeDown()
    {
        $this->dropTable('redirects');
    }
}
