<?php

namespace modules\v1\account\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%user_settings}}".
 *
 * @property integer $id
 * @property string $db
 * @property integer $company_id
 * @property string $region
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property User $id0
 */
class GlbCompany extends \yii\db\ActiveRecord
{
    const STATUS_DELETED = 0;
    const STATUS_UNVERIFIED = 5;
    const STATUS_VERIFIED = 6;
    const STATUS_ACTIVE = 10;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%company}}';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('glb_sys_db_01');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['company_id', 'region',], 'required'],
            [['company_id'], 'integer'],
            [['db', 'region'], 'string', 'max' => 255],
        ];
    }

    public function getUser()
    {
        return $this->hasOne(GlbUser::className(), ['company_id' => 'company_id']);
    }
}
