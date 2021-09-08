<?php

use yii\db\Schema;
use yii\db\Migration;
use app\models\User;

class m150715_131835_add_users_table extends Migration {
	public function safeUp() {
		$cnt = 0;
		$this->createTable('users', [
			'id' => Schema::TYPE_PK,
			'telegram_id' => Schema::TYPE_INTEGER . ' NOT NULL',
			'first_name' => Schema::TYPE_STRING,
			'last_name' => Schema::TYPE_STRING,
			'username' => Schema::TYPE_STRING,
			'created_at' => Schema::TYPE_DATETIME . ' NOT NULL',
			'updated_at' => Schema::TYPE_DATETIME . ' NOT NULL',
		]);
		$path = Yii::getAlias('@app/storage/');
		$files = scandir($path);
		foreach ($files as $file) {
			if ($file == '.' || $file == '..' || $file == '.gitignore') {
				continue;
			}
			$content = file_get_contents($path . $file);
			if (!empty($content)) {
				$content = json_decode($content, true);

				if (!empty($content['message'])) {
					$user = $content['message'];
				} elseif (!empty($content['request']['message'])) {
					$user = $content['request']['message'];
				} else {
					continue;
				}

				$data=$user['from'];
				$data['created_at']=$data['updated_at']= date('Y-m-d H:i:s', $user['date']);
				if (User::createIfNotExist($data)===true){
					$cnt++;
				}
			}
		}
		echo "Inserted {$cnt} records\n";
	}

	public function safeDown() {
		$this->dropTable('users');
	}
	/*
	// Use safeUp/safeDown to run migration code within a transaction
	public function safeUp()
	{
	}

	public function safeDown()
	{
	}
	*/
}
