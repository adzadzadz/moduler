<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\db\Command;
use yii\db\Schema;

class FncProjectAction extends ActiveRecord
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
        return '{{%' . Yii::$app->strepzConfig->company_id . '_project_action}}';
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
            [['topic', 'description', 'reported_by', 'action_owner', 'priority', 'due_date'], 'required'],
            [['project_id', 'reported_by', 'action_owner', 'next_action_owner', 'priority', 'status', 'due_date', 'created_at', 'updated_at', 'author_id', 'updater_id'], 'integer'],
            [['topic', 'description'], 'string', 'max' => 255],
        ];
    }

    /**
     * This is where tables are initialized. All required tables should be added here.
     * @return Boolean
     */
    public function initProjectActionTables()
    {
        return $this->createActionTable();
    }

    private function createActionTable()
    {
        $db = $this->getDb();
        $result = $db->createCommand()->createTable( static::tableName(), [
            'id' => Schema::TYPE_PK,
            'project_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'topic' => Schema::TYPE_STRING . ' NOT NULL',
            'description' => Schema::TYPE_STRING . ' NOT NULL',

            'reported_by' => Schema::TYPE_INTEGER . ' NOT NULL',
            'action_owner' => Schema::TYPE_INTEGER . ' NOT NULL',
            'next_action_owner' => Schema::TYPE_INTEGER . ' NOT NULL',
            'priority' => Schema::TYPE_INTEGER . ' NOT NULL',
            
            'status' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 10',
            'due_date' => Schema::TYPE_INTEGER . ' NOT NULL',
            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'author_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updater_id' => Schema::TYPE_INTEGER . ' NOT NULL',
        ])->execute();

        $db->createCommand()
            ->addForeignKey( 
                $name = 'fk_' . Yii::$app->strepzConfig->company_id . '_project_action_id', 
                $table = static::tableName(),
                $columns = 'project_id',
                $refTable = FncProject::tableName(), 
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

    public function create()
    {
        if ($this->validate()) {
            $model = new FncProjectAction;
            $model->project_id = Yii::$app->strepzConfig->selectedProject;
            $model->topic = $this->topic;
            $model->description = $this->description;

            $model->reported_by = Yii::$app->user->id;
            $model->action_owner = $this->action_owner;
            $model->next_action_owner = 0;
            $model->priority = $this->priority;

            $model->due_date = $this->due_date;
            $model->status = 10;

            if ($model->save()) {
                return $model;
            }
        }
        return false;
    }

}