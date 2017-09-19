<?php

use yii\db\Schema;
use yii\db\Migration;

class m150829_052527_create_global_users_table extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%user}}', [
            'id' => Schema::TYPE_PK,
            'company_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'user_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'username' => Schema::TYPE_STRING . ' NOT NULL',
            'type' => Schema::TYPE_INTEGER . ' NOT NULL', #must be 'owner' or 'member'
            'status' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 5',
        ], $tableOptions);
        
        // $this->addPrimaryKey( 'pk_uid', '{{%user}}', ['user_id'] );
        $this->createIndex ( 'comp_id', '{{%user}}', ['company_id'] );
    }

    public function down()
    {
        echo "m150829_052527_create_global_users_table cannot be reverted.\n";

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
