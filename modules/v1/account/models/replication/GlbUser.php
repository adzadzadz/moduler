<?php

namespace api\modules\v1\account\models\replication;

use Yii;
use common\components\DbDataReplicator;

class GlbUser extends \yii\db\ActiveRecord
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
        return '{{%user}}';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->db_rep->current_glbUserDb;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'company_id' => Yii::t('model', 'Company ID'),
            'username' => Yii::t('model', 'Username'),
        ];
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne([
            'id' => $id, 
            'status' => self::STATUS_UNVERIFIED
        ]);
    }

    public static function getUserData($username)
    {
        $glbUser = GlbUser::find()
            ->joinWith('userCompany')
            ->where([
                'and', 
                ['{{%user}}.username' => $username], 
                ['>=', '{{%user_company}}.status', GlbUser::STATUS_UNVERIFIED],
            ])->all();

        if ($glbUser !== null) {
            return $glbUser;
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

    public function getUserCompany()
    {
        return $this->hasMany(GlbUserCompany::className(), ['company_id' => 'company_id']);
    }
}