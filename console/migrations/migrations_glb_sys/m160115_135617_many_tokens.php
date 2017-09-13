<?php

/**
 * Generate model in console:
 *
 * ./yii migrate
 * ./yii gii/model --tableName=user_token --modelClass=UserToken --ns=common\\models
 */

use yii\db\Migration;
use yii\db\Schema;
use yii\base\Event;

class m160115_135617_many_tokens extends Migration
{
   
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%user_token}}', [
            'token' => $this->string(128)->notNull(),
            'company_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'user_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'verify_ip' => $this->boolean()->defaultValue(false),
            
            // @link http://stackoverflow.com/a/20473371
            'user_ip' => $this->string(46),
            
            // @link http://stackoverflow.com/a/20746656
            'user_agent' => $this->text(),
            
            'frozen_expire'  => $this->boolean()->defaultValue(true),
            
            'status' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 5',
            'created_at' => $this->dateTime(),
            'expired_at'  => $this->dateTime(),
        ], $tableOptions);

        $this->addPrimaryKey( 'pk_token', '{{%user_token}}', ['token'] );
    }

    public function down()
    {
        
    }
}
