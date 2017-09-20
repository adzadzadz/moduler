<?php

namespace api\modules\v1\account\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\base\NotSupportedException;
use yii\web\IdentityInterface;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\db\Command;
use yii\db\Schema;
use yii\web\Session;
use common\config\models\rbac\AuthAssignment;
use common\config\models\rbac\AuthItem;
use yii\rbac\DbManager;

class FncRbac extends Model
{
    public $company_id = null;

    public static function getDb()
    {
        // Functional Database - EU
        // return StrepzDbManager::getFncDb();
        return Yii::$app->strepzDbManager->getFncDb();
    }

    /**
     * This is where tables are initialized. All required tables should be added here.
     * @return Boolean
     */
    public function initTables()
    {        
        return $this->createRbacTables();
    }

    private function createRbacTables()
    {
        $authManager = Yii::$app->getAuthManager();
        $authManager->ruleTable = '{{%' . Yii::$app->strepzConfig->company_id . '_auth_rule}}';
        $authManager->itemTable = '{{%' . Yii::$app->strepzConfig->company_id . '_auth_item}}';
        $authManager->itemChildTable = '{{%' . Yii::$app->strepzConfig->company_id . '_auth_item_child}}';
        $authManager->assignmentTable = '{{%' . Yii::$app->strepzConfig->company_id . '_auth_assignment}}';

        $db = $this->getDb();

        $db->createCommand()->createTable($authManager->ruleTable, [
            'name' => 'varchar(64) NOT NULL',
            'data' => Schema::TYPE_TEXT,
            'created_at' => Schema::TYPE_INTEGER,
            'updated_at' => Schema::TYPE_INTEGER,
            'PRIMARY KEY (name)',
        ])->execute();

        $db->createCommand()->createTable($authManager->itemTable, [
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
        $db->createCommand()->createIndex('idx-auth_item-type', $authManager->itemTable, 'type')->execute();

        $db->createCommand()->createTable($authManager->itemChildTable, [
            'parent' => Schema::TYPE_STRING . ' NOT NULL',
            'child' => Schema::TYPE_STRING . ' NOT NULL',
            'PRIMARY KEY (parent, child)',
            'FOREIGN KEY (parent) REFERENCES ' . $authManager->itemTable . ' (name) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY (child) REFERENCES ' . $authManager->itemTable . ' (name) ON DELETE CASCADE ON UPDATE CASCADE',
        ])->execute();

        $db->createCommand()->createTable($authManager->assignmentTable, [
            'item_name' => Schema::TYPE_STRING . ' NOT NULL',
            'user_id' => Schema::TYPE_STRING . ' NOT NULL',
            'project_id' => Schema::TYPE_INTEGER,
            'created_at' => Schema::TYPE_INTEGER,
            'PRIMARY KEY (item_name, user_id)',
            'FOREIGN KEY (item_name) REFERENCES ' . $authManager->itemTable . ' (name) ON DELETE CASCADE ON UPDATE CASCADE',
        ])->execute();

        $superadmin = \api\modules\v1\account\models\rbac\Rbac::initRoles($authManager);
        return true;
    }
}