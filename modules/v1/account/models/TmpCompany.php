<?php

namespace modules\v1\account\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\db\Command;
use yii\db\Schema;


class TmpCompany extends ActiveRecord
{
    const STATUS_DELETED = 0;
    const STATUS_UNVERIFIED = 5;
    const STATUS_ACTIVE = 10;

    public $company_id = null;

    public static function getDb()
    {
        return Yii::$app->glb_reg_db_01;
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

    // This is where tables are initialized. All required tables should be added here.
    public function initCompanyTables($company_id)
    {
        $this->company_id = $company_id;

        // Initialize company data table
        if ($this->createMainTableSchema()) {
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