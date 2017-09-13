<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "{{%user_token}}".
 *
 * @property integer $id
 * @property integer $company_id
 * @property integer $user_id
 * @property string $token
 * @property integer $verify_ip
 * @property string $user_ip
 * @property string $user_agent
 * @property integer $frozen_expire
 * @property string $created_at
 * @property string $expired_at
 */
class GlbUserToken extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_token}}';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => false,
                'value' => new Expression('NOW()'),
            ],
        ];
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
            [['company_id', 'user_id', 'token'], 'required'],
            [['company_id', 'user_id', 'verify_ip', 'frozen_expire'], 'integer'],
            [['user_agent'], 'string'],
            [['created_at', 'expired_at'], 'safe'],
            [['token'], 'string', 'max' => 128],
            [['user_ip'], 'string', 'max' => 46],

            ['status', 'default', 'value' => GlbUser::STATUS_UNVERIFIED],
            ['status', 'in', 'range' => [GlbUser::STATUS_ACTIVE, GlbUser::STATUS_UNVERIFIED,  GlbUser::STATUS_VERIFIED, GlbUser::STATUS_DELETED]],
        ];
    }

    public static function setStrepzConfig($token)
    {        
        // Find the token data
        if ($userToken = static::findOne($token)) {
            $glbUser = GlbUser::findOne([
                'company_id' => $userToken->company_id, 
                'user_id' => $userToken->user_id
            ]);
            Yii::$app->strepzConfig->setIsTempUser($glbUser->status);
            Yii::$app->strepzConfig->setCompanyId($glbUser->company_id);
            return true;
        }
        return false;
    }

    public static function generateUniqueToken()
    {
        $token = Yii::$app->security->generateRandomString(128);
        $userToken = GlbUserToken::findOne($token);
        if (!$userToken) {
            return $token;
        }
        return GlbUserToken::generateUniqueToken();
    }

    public static function getToken($company_id, $user_id)
    {
        if ($userToken = GlbUserToken::findOne(['company_id' => $company_id, 'user_id' => $user_id])) {
            return $userToken->token;
        }
        return null;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'company_id' => 'Company ID',
            'user_id' => 'User ID',
            'token' => 'Token',
            'verify_ip' => 'Verify Ip',
            'user_ip' => 'User Ip',
            'user_agent' => 'User Agent',
            'frozen_expire' => 'Frozen Expire',
            'created_at' => 'Created At',
            'expired_at' => 'Expired At',
        ];
    }
}
