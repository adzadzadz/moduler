<?php

use yii\db\Schema;
use yii\db\Migration;

class m150919_043044_create_db_balancer_table extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%fnc_db_balancer}}', [
            'db_id' => Schema::TYPE_STRING . ' NOT NULL',
            'db_load_limit' => Schema::TYPE_INTEGER . ' NOT NULL',
            'db_current_load' => Schema::TYPE_INTEGER . ' NOT NULL',
            'status' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 10',
        ], $tableOptions);

        $this->addPrimaryKey( '', '{{%fnc_db_balancer}}', ['db_id'] );

        $balancer = new \common\models\GlbDbBalancer;
        $balancer->db_id = "fnc_db_01";
        $balancer->db_load_limit = 100;
        $balancer->db_current_load = 0;
        $balancer->save();
    }

    public function down()
    {
        echo "m150919_043044_create_db_balancer_table cannot be reverted.\n";

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