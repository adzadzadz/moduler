<?php

use yii\db\Schema;
use yii\db\Migration;

class m150912_075333_create_user_company_table extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%company}}', [
            'company_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'db' => Schema::TYPE_STRING . ' NOT NULL default ""',
            'region' => Schema::TYPE_STRING . ' NOT NULL',
            'ipaddress' => Schema::TYPE_STRING . ' NOT NULL',
        ], $tableOptions);

        $this->addPrimaryKey( 'pk_company_id', '{{%company}}', ['company_id'] );
        $this->addForeignKey( 'fk_user_id', '{{%user}}', 'company_id', '{{%company}}', 'company_id', $delete = 'CASCADE', $update = null );

    }

    public function down()
    {
        echo "m150829_085435_create_setting_table cannot be reverted.\n";

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
