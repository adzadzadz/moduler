<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\base\NotSupportedException;

class GlbUser extends \yii\db\ActiveRecord
{
    const STATUS_DELETED = 0;
    const STATUS_UNVERIFIED = 5;
    const STATUS_VERIFIED = 6;
    const STATUS_ACTIVE = 10;
    const TYPE_OWNER = 10;
    const TYPE_MEMBER = 5;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('glb_sys_db_01');
    }

    public function rules()
    {
        return [
            [['company_id', 'username', 'type'], 'required'],
            [['company_id', 'type', 'status', 'user_id'], 'integer'],
            ['type', 'default', 'value' => self::TYPE_MEMBER],
            ['status', 'default', 'value' => self::STATUS_UNVERIFIED],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_UNVERIFIED, self::STATUS_VERIFIED, self::STATUS_DELETED]],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'company_id' => 'Company ID',
            'username' => 'Username',
            'user_id' => 'User ID',
            'type' => 'Type',
            'status' => 'Status'
        ];
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne([
            'id' => $id, 
        ]);
    }

    public static function getUserData($username)
    {
        $user = GlbUser::findOne(['username' => $username]);
        if ($user !== null) {
            return $user;
        }
        return false;
    }

    public static function findByUsername($username)
    {
        return static::findOne([
            'username' => $username,
            // 'status' => self::STATUS_ACTIVE,
        ]);
    }

    public function getCompany()
    {
        return $this->hasOne(GlbCompany::className(), ['company_id' => 'company_id']);
    }
}
