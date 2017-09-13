<?php

namespace common\models\replication;

use Yii;
use common\components\DbDataReplicator;

class GlbUserCompany extends \yii\db\ActiveRecord
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
        return '{{%user_company}}';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        // return Yii::$app->get('glb_sys_db_01');
        return Yii::$app->db_rep->current_glbUserDb;
    }

    /**
     * @inheritdoc
     */
    // public function rules()
    // {
    //     return [
    //         // [['company_id', 'region',], 'required'],
    //         [['company_id', 'status', 'user_id'], 'integer'],
    //         ['status', 'default', 'value' => self::STATUS_UNVERIFIED],
    //         [['db', 'region'], 'string', 'max' => 255],

    //         ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_UNVERIFIED, self::STATUS_VERIFIED, self::STATUS_DELETED]],
    //     ];
    // }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getId0()
    {
        return $this->hasOne(GlbUser::className(), ['company_id' => 'company_id']);
    }

    public function getUser()
    {
        return $this->hasOne(GlbUser::className(), ['company_id' => 'company_id']);
    }
}
