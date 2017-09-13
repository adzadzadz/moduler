<?php

namespace api\modules\v1\account\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\db\Command;
use yii\db\Schema;

class FncConfig extends ActiveRecord
{
    const STATUS_DELETED = 0;
    const STATUS_UNVERIFIED = 5;
    const STATUS_ACTIVE = 10;

    public $selectedProjectName = 'selectedProject';
    public $languageName = 'language';

    public $userDefaultType = 'userDefault';

    public static function getDb()
    {
        // Functional Database - EU
        // return StrepzDbManager::getFncDb();
        return Yii::$app->strepzDbManager->getFncDb();
    }

    public static function tableName()
    {
        return '{{%' . Yii::$app->strepzConfig->company_id . '_config}}';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    public function rules()
    {
        return [
            [['user_id', 'type', 'name', 'value'], 'required'],

            ['user_id', 'integer'],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_UNVERIFIED, self::STATUS_DELETED]],
        ];
    }

    public static function selectProject($project_id)
    {
        $config = new FncConfig;
        if ($result = $config->setSelectedProject($project_id)) {
            return $result;
        }
        return false;
    }

    public static function getSelectedProject()
    {
        $config = new FncConfig;
        return $config->getProject();
    }

    public function getProject()
    {
        $config = FncConfig::findOne([
            'user_id' => Yii::$app->user->id, 
            'type' => $this->userDefaultType, 
            'name' => $this->selectedProjectName
        ]);
        if (is_null($config)) {
            if (!$config = $this->createSelectedProject()) {
                return false;
            }
        }
        $project = FncProject::findOne($config->value);
        if (!is_null($project)) {
            return $project;    
        }
        return false;

    }

    public function setSelectedProject($project_id)
    {
        $config = FncConfig::findOne([
            'user_id' => Yii::$app->user->id, 
            'type' => $this->userDefaultType, 
            'name' => $this->selectedProjectName
        ]);
        if (is_null($config)) {
            if (!$this->createSelectedProject()) {
                return false;
            }
        }
        $config->value = $project_id;
        if ($config->save()) {
            return $config;
        }
        return false;
    }

    public function createSelectedProject()
    {
        $config = new FncConfig;
        $config->user_id = Yii::$app->user->id;
        $config->type = $this->userDefaultType;
        $config->name = $this->selectedProjectName;
        $config->value = 0;

        if ($config->save()) {
            return $config;
        }
     
        return false;
    }

    /**
     * This is where tables are initialized. All required tables should be added here.
     * @return Boolean
     */
    public function initConfigTables()
    {
        return $this->createCompanyConfigTable();
    }

    private function createCompanyConfigTable()
    {
        $db = $this->getDb();
        $result = $db->createCommand()->createTable( static::tableName() , [
            'id' => Schema::TYPE_PK,
            'user_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'type' => Schema::TYPE_STRING . ' NOT NULL',
            'name' => Schema::TYPE_STRING . ' NOT NULL',
            'value' => Schema::TYPE_STRING . ' NOT NULL',
            
            'status' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 10',
            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL',
        ])->execute();

        $db->createCommand()->addForeignKey( 
            'fk_' . Yii::$app->strepzConfig->company_id . '_config_user_id', 
            '{{%' . Yii::$app->strepzConfig->company_id . '_config}}', 
            'user_id', '{{%' . Yii::$app->strepzConfig->company_id . 
            '_user}}', 
            'id', 
            $delete = 'CASCADE', 
            $update = 'RESTRICT' )->execute();

        
        // int 0 means success in this section. 
        if ($result === 0) {
            return true;
        }
        return false;
    }

}