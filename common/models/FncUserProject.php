<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\db\Command;
use yii\db\Schema;

class FncUserProject extends ActiveRecord
{
    const STATUS_DELETED = 0;
    const STATUS_UNVERIFIED = 5;
    const STATUS_ACTIVE = 10;

    public static function getDb()
    {
        // Functional Database - EU
        // return StrepzDbManager::getFncDb();
        return Yii::$app->strepzDbManager->getFncDb();
    }

    public static function tableName()
    {
        return '{{%' . Yii::$app->strepzConfig->company_id . '_user_project}}';
    }

    public function rules()
    {
        return [
            [['user_id', 'project_id'], 'required'],
            [['user_id', 'project_id'], 'integer']
        ];
    }

    /**
     * This is where tables are initialized. All required tables should be added here.
     * @return Boolean
     */
    public function initUserProjectTables()
    {
        return $this->createUserProjectTable();
    }

    private function createUserProjectTable()
    {
        $db = $this->getDb();
        $result = $db->createCommand()->createTable( static::tableName(), [
            'user_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'project_id' => Schema::TYPE_INTEGER . ' NOT NULL',
        ])->execute();

        $db->createCommand()
            ->addPrimaryKey( 
                $name = 'pk_' . Yii::$app->strepzConfig->company_id . '_user_project', 
                $table = static::tableName(),
                $columns = ['user_id', 'project_id'] )
                // $unique = false )
            ->execute();

        $db->createCommand()
            ->addForeignKey( 
                $name = 'fk_' . Yii::$app->strepzConfig->company_id . '_user_id_project', 
                $table = static::tableName(), 
                $columns = 'user_id',
                $refTable = FncUser::tableName(), 
                $refColumns = 'id', 
                $delete = 'CASCADE', 
                $update = 'RESTRICT' )
            ->execute();

        $db->createCommand()
            ->addForeignKey( 
                $name = 'fk_' . Yii::$app->strepzConfig->company_id . '_user_project_id', 
                $table = static::tableName(), 
                $columns = 'project_id',
                $refTable = FncProject::tableName(), 
                $refColumns = 'id',
                $delete = 'CASCADE',
                $update = 'RESTRICT' )
            ->execute();

        // WEIRDLY, 0 means success on this section. 
        if ($result === 0) {
            return true;
        }
        return false;
    }

    public function getProject()
    {
        return $this->hasMany(FncProject::className(), ['id' => 'project_id']);
    }

    public function getUser()
    {
        return $this->hasMany(FncUser::className(), ['id' => 'user_id']);   
    }

    public function getAuthAssignment()
    {
        return $this->hasMany(\common\models\rbac\AuthAssignment::className(), ['user_id' => 'user_id']);
    }

}