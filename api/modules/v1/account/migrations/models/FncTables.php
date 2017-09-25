<?php 

namespace frontend\migrations;

use Yii;

class FncTables extends \yii\base\Object
{
    public $version = '0.0.1';

    /**
     * Yii::$app->strepzDbManager->getFncDb();
     */
    public $db;
    public $company_id;

    public function up()
    {
        $this->upUser();
        // $this->upRbac();
        // $this->upCompany();
        // $this->upConfig();

        return true;
    }

    private function upUser()
    {
        $db = Yii::$app->get('strepz_test_fnc_db_01');
        $db->createCommand()->createTable( '{{%' . $this->company_id . '_user}}' , [
            'id' => Schema::TYPE_PK,
            'username' => Schema::TYPE_STRING . ' NOT NULL',
            'auth_key' => Schema::TYPE_STRING . '(32) NOT NULL',
            'password_hash' => Schema::TYPE_STRING . ' NOT NULL',
            'password_reset_token' => Schema::TYPE_STRING,
            'email' => Schema::TYPE_STRING . ' NOT NULL',

            'firstname' => Schema::TYPE_STRING . ' NOT NULL',
            'middlename' => Schema::TYPE_STRING . ' NOT NULL',
            'lastname' => Schema::TYPE_STRING . ' NOT NULL',
            'mobile' => Schema::TYPE_INTEGER . ' NOT NULL',
            'phone' => Schema::TYPE_INTEGER . ' NOT NULL',
            'role' => Schema::TYPE_STRING . ' NOT NULL',

            'ipaddress' => Schema::TYPE_STRING . ' NOT NULL',

            'verification_code' => Schema::TYPE_STRING . ' NOT NULL',
            'status' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 5',
            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL',

        ])->execute();
    }

    private function upRbac()
    {
        $authManager = Yii::$app->getAuthManager();
        $authManager->ruleTable = '{{%' . $this->company_id . '_auth_rule}}';
        $authManager->itemTable = '{{%' . $this->company_id . '_auth_item}}';
        $authManager->itemChildTable = '{{%' . $this->company_id . '_auth_item_child}}';
        $authManager->assignmentTable = '{{%' . $this->company_id . '_auth_assignment}}';

        $this->db->createCommand()->createTable($authManager->ruleTable, [
            'name' => 'varchar(64) NOT NULL',
            'data' => Schema::TYPE_TEXT,
            'created_at' => Schema::TYPE_INTEGER,
            'updated_at' => Schema::TYPE_INTEGER,
            'PRIMARY KEY (name)',
        ])->execute();

        $this->db->createCommand()->createTable($authManager->itemTable, [
            'name' => 'varchar(64) NOT NULL',
            'type' => Schema::TYPE_INTEGER . ' NOT NULL',
            'description' => Schema::TYPE_STRING,
            'rule_name' => 'varchar(64)',
            'data' => Schema::TYPE_STRING,
            'created_at' => Schema::TYPE_INTEGER,
            'updated_at' => Schema::TYPE_INTEGER,
            'PRIMARY KEY (name)',
            'FOREIGN KEY (rule_name) REFERENCES ' . $authManager->ruleTable . ' (name) ON DELETE SET NULL ON UPDATE CASCADE',
        ])->execute();
        $this->db->createCommand()->createIndex('idx-auth_item-type', $authManager->itemTable, 'type')->execute();

        $this->db->createCommand()->createTable($authManager->itemChildTable, [
            'parent' => Schema::TYPE_STRING . ' NOT NULL',
            'child' => Schema::TYPE_STRING . ' NOT NULL',
            'PRIMARY KEY (parent, child)',
            'FOREIGN KEY (parent) REFERENCES ' . $authManager->itemTable . ' (name) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY (child) REFERENCES ' . $authManager->itemTable . ' (name) ON DELETE CASCADE ON UPDATE CASCADE',
        ])->execute();

        $this->db->createCommand()->createTable($authManager->assignmentTable, [
            'item_name' => Schema::TYPE_STRING . ' NOT NULL',
            'user_id' => Schema::TYPE_STRING . ' NOT NULL',
            'project_id' => Schema::TYPE_INTEGER,
            'created_at' => Schema::TYPE_INTEGER,
            'PRIMARY KEY (item_name, user_id)',
            'FOREIGN KEY (item_name) REFERENCES ' . $authManager->itemTable . ' (name) ON DELETE CASCADE ON UPDATE CASCADE',
        ])->execute();

        $superadmin = \common\models\rbac\Rbac::initRoles($authManager);
    }

    private function upCompany()
    {
        $this->db->createCommand()->createTable( '{{%' . $this->company_id . '_company}}' , [
            'id' => Schema::TYPE_PK,
            'name' => Schema::TYPE_STRING . ' NOT NULL',
            'address' => Schema::TYPE_STRING . ' NOT NULL',
            'postal_code' => Schema::TYPE_STRING . ' NOT NULL',
            'city' => Schema::TYPE_STRING . ' NOT NULL',
            'state' => Schema::TYPE_STRING . ' NOT NULL',
            'country' => Schema::TYPE_STRING . ' NOT NULL',
            'phone' => Schema::TYPE_STRING . ' NOT NULL',
            'fax' => Schema::TYPE_STRING . ' NOT NULL',
            'email' => Schema::TYPE_STRING . ' NOT NULL',
            'website' => Schema::TYPE_STRING . ' NOT NULL',

            'type' => Schema::TYPE_STRING . ' NOT NULL',
            'size' => Schema::TYPE_STRING . ' NOT NULL',

            'status' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 10',
            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL',
        ])->execute();
    }

    private function upConfig()
    {
        $this->db->createCommand()->createTable( '{{%' . $this->company_id . '_config}}' , [
            'id' => Schema::TYPE_PK,
            'user_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'type' => Schema::TYPE_STRING . ' NOT NULL',
            'name' => Schema::TYPE_STRING . ' NOT NULL',
            'value' => Schema::TYPE_STRING . ' NOT NULL',
            
            'status' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 10',
            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL',
        ])->execute();

        $this->db->createCommand()->addForeignKey( 
            'fk_' . $this->company_id . '_config_user_id', 
            '{{%' . $this->company_id . '_config}}', 
            'user_id', 
            '{{%' . $this->company_id . '_user}}', 
            'id', 
            $delete = 'CASCADE', 
            $update = 'RESTRICT' )->execute();
    }
}