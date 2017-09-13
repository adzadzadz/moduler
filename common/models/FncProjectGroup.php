<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\db\Command;
use yii\db\Schema;


class FncProjectGroup extends ActiveRecord
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
        return '{{%' . Yii::$app->strepzConfig->company_id . '_project_group}}';
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
            [['name', 'description'], 'string', 'max' => 255],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_UNVERIFIED, self::STATUS_DELETED]],
        ];
    }

    /**
     * This is where tables are initialized. All required tables should be added here.
     * @return Boolean
     */
    public function initProjectGroupTables()
    {
        return $this->createProjectGroupTable();
    }

    private function createProjectGroupTable()
    {
        $db = $this->getDb();
        $result = $db->createCommand()->createTable( static::tableName(), [
            'id' => Schema::TYPE_PK,
            'name' => Schema::TYPE_STRING . ' NOT NULL',
            'description' => Schema::TYPE_STRING,
            
            'status' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 10',
            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'author_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updater_id' => Schema::TYPE_INTEGER . ' NOT NULL',
        ])->execute();

        // WEIRDLY, 0 means success on this section. 
        if ($this->setDefaultGroup() && $result === 0) {
            return true;
        }
        return false;
    }

    private function setDefaultGroup()
    {
        $group = new Self;
        $group->name = "uncategorized";

        if ($group->save()) {
            return $group;
        }
        return false;
    }

    public static function getAll($username)
    {
        $glbUser = GlbUser::find()
            ->joinWith('projects')->all();

        if ($glbUser !== null) {
            return $glbUser;
        }

        return false;
    }

    public function getProjects()
    {
        return $this->hasMany(FncProject::className(), ['project_group_id' => 'id']);
    }
}