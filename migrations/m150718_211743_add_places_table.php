<?php

use yii\db\Schema;
use yii\db\Migration;

class m150718_211743_add_places_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('places', [
            'id' => Schema::TYPE_PK,
            'name' => Schema::TYPE_STRING . ' NOT NULL',
            'address' => Schema::TYPE_STRING . ' DEFAULT NULL',
            'yandex_id' => Schema::TYPE_STRING . ' NOT NULL',
            'website' => Schema::TYPE_STRING . ' DEFAULT NULL',
            'average_bill' => Schema::TYPE_STRING . ' DEFAULT NULL',
            'active' => Schema::TYPE_BOOLEAN . ' NOT NULL DEFAULT TRUE',
            'location' => 'geometry NOT NULL',
            'latitude' => Schema::TYPE_DOUBLE . ' NOT NULL',
            'longitude' => Schema::TYPE_DOUBLE . ' NOT NULL',
            'point' => 'point NOT NULL',
            'created_at' => Schema::TYPE_DATETIME . ' NOT NULL',
            'updated_at' => Schema::TYPE_DATETIME . ' NOT NULL',
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('places');
    }
}
