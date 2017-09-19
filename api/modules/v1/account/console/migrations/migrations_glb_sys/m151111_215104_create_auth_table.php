<?php

use yii\db\Schema;
use yii\db\Migration;

class m151111_215104_create_auth_table extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%user_auth}}', [
            'auth_id' => Schema::TYPE_PK,
            'user_id' => Schema::TYPE_INTEGER,
            'username' => Schema::TYPE_STRING . ' NOT NULL',
            'source' => Schema::TYPE_STRING . ' NOT NULL',
            'source_id' => Schema::TYPE_STRING . ' NOT NULL',
        ], $tableOptions);

    }

    public function down()
    {
        echo "m151111_215104_create_auth_table cannot be reverted.\n";

        return false;
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
