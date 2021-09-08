<?php

use yii\db\Schema;
use yii\db\Migration;

class m150721_102050_fix_fields_in_users_to_null extends Migration
{
    public function safeUp()
    {
        $this->alterColumn('users', 'first_name', 'SET DEFAULT NULL');
        $this->alterColumn('users', 'last_name', 'SET DEFAULT NULL');
        $this->alterColumn('users', 'username', 'SET DEFAULT NULL');
    }

    public function safeDown()
    {
        $this->alterColumn('users', 'first_name', 'DROP DEFAULT');
        $this->alterColumn('users', 'last_name', 'DROP DEFAULT');
        $this->alterColumn('users', 'username', 'DROP DEFAULT');
    }
}
