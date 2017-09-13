<?php

namespace api\modules\v1\account\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\db\Command;
use yii\db\Schema;


class FncCompany extends ActiveRecord
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
        return '{{%' . Yii::$app->strepzConfig->company_id . '_company}}';
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
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_UNVERIFIED, self::STATUS_DELETED]],
        ];
    }

    /**
     * This is where tables are initialized. All required tables should be added here.
     * @return Boolean
     */
    public function initCompanyTables()
    {
        // Initialize company data table
        return $this->createMainTableSchema();
    }

    private function createMainTableSchema()
    {
        $db = $this->getDb();
        $result = $db->createCommand()->createTable( static::tableName() , [
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

        // 0 means success in this section. 
        if ($result === 0) {
            return true;
        }
        return false;
    }

    public function checkTable($tableName)
    {
        $db = $this->getDb();
        return $db->schema->getTableSchema($tableName);
    }
}