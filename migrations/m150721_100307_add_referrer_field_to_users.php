<?php

use yii\db\Schema;
use yii\db\Migration;

class m150721_100307_add_referrer_field_to_users extends Migration
{
    public function safeUp()
    {
        $this->addColumn('users', 'referrer', Schema::TYPE_STRING . ' DEFAULT NULL');
    }

    public function safeDown()
    {
        $this->dropColumn('users', 'referrer');
    }
}
