<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\db\Command;
use yii\db\Schema;

class FncProject extends ActiveRecord
{
    const STATUS_DELETED = 0;
    const STATUS_UNVERIFIED = 5;
    const STATUS_ACTIVE = 10;

    public function init()
    {
        parent::init();
    }

    public static function getDb()
    {
        // Functional Database - EU
        // return StrepzDbManager::getFncDb();
        return Yii::$app->strepzDbManager->getFncDb();
    }

    public static function tableName()
    {
        return '{{%' . Yii::$app->strepzConfig->company_id . '_project}}';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
            [
                'class' => BlameableBehavior::className(),
                'createdByAttribute' => 'author_id',
                'updatedByAttribute' => 'updater_id',
            ],
        ];
    }

    public function rules()
    {
        return [
            ['name', 'string', 'max' => 255],
            ['project_group_id', 'default', 'value' => 1],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_UNVERIFIED, self::STATUS_DELETED]],
        ];
    }

    /**
     * This is where tables are initialized. All required tables should be added here.
     * @return Boolean
     */
    public function initProjectTables()
    {
        $group = new FncProjectGroup;
        $userProject = new FncUserProject;
        $projectAction = new FncProjectAction;

        if ($group->initProjectGroupTables() && 
            $this->createProjectTable() && 
            $projectAction->initProjectActionTables() &&
            $userProject->initUserProjectTables()) {

            return true;
        }
        return false;
    }

    private function createProjectTable()
    {
        $db = $this->getDb();
        $result = $db->createCommand()->createTable( static::tableName(), [
            'id' => Schema::TYPE_PK,
            'project_group_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'name' => Schema::TYPE_STRING . ' NOT NULL',
            
            'status' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 10',
            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'author_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updater_id' => Schema::TYPE_INTEGER . ' NOT NULL',
        ])->execute();

        $db->createCommand()
            ->createIndex ( 
                $name = 'idx_' . Yii::$app->strepzConfig->company_id . '_project_group_id', 
                $table = static::tableName(), 
                $columns = 'project_group_id', 
                $unique = false )
            ->execute();

        $db->createCommand()
            ->addForeignKey( 
                $name = 'fk_' . Yii::$app->strepzConfig->company_id . '_project_group_id', 
                $table = static::tableName(), 
                $columns = 'project_group_id',
                $refTable = FncProjectGroup::tableName(), 
                $refColumns = 'id', 
                $delete = null, 
                $update = 'RESTRICT' )
            ->execute();

        // WEIRDLY, 0 means success on this section. 
        if ($result === 0) {
            return true;
        }
        return false;
    }

}