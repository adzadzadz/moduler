<?php

namespace modules\v1\account\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\base\NotSupportedException;
use yii\web\IdentityInterface;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\db\Command;
use yii\db\Schema;
use yii\web\Session;

class TmpUser extends ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_UNVERIFIED = 5;
    const STATUS_VERIFIED = 6;
    const STATUS_ACTIVE = 10;

    public $company_id = null;

    private $_glbUser = false;
    private $_fncUser = false;
    private $_tmpUser = false;

    public static function getDb()
    {
        return Yii::$app->glb_reg_db_01;
    }

    public static function tableName()
    {
        return '{{%' . Yii::$app->strepzConfig->company_id . '_user}}';
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
            ['status', 'default', 'value' => self::STATUS_UNVERIFIED],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_UNVERIFIED, self::STATUS_VERIFIED, self::STATUS_DELETED]],
        ];
    }

    public function deleteData()
    {
        $db = $this->getDb();
        if ($db->schema->getTableSchema('{{%' . Yii::$app->strepzConfig->company_id . '_user}}') !== null) {
            $db->createCommand()->dropTable('{{%' . Yii::$app->strepzConfig->company_id . '_user}}')->execute();
        }        
    }

    public function getVerificationCode()
    {
        $this->username = Yii::$app->user->identity->username;
        $user = $this->getUser();

        if ($user !== false) {
            return $user->verification_code;
        }

        return $user;
    }

    public function getUser($username = null)
    {
        if ($username !== null) {
            $this->username = $username;
        } else {
            $this->username = Yii::$app->user->identity->username;
        }
        
        $this->_glbUser = GlbUser::getUserData($this->username);
        if ($this->_glbUser !== false) {
            $company_id = $this->_glbUser['company']->company_id;
            Yii::$app->strepzConfig->setCompanyId($company_id);

            $this->_tmpUser = TmpUser::findOne(['username' => $this->username]);
        }
        return $this->_tmpUser;
    }

    public static function findUserByRegistrationToken($username, $token)
    {
        return TmpUser::findOne([
            'username' => $username,
            '_registration_token' => $token
        ]);
    }

    public static function findIdentity($id)
    {
        return static::findOne([
            'user_id' => $id, 
            // 'status' => self::STATUS_UNVERIFIED,
        ]);
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne([
            'username' => $username, 
            // 'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return boolean
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        $parts = explode('_', $token);
        $timestamp = (int) end($parts);
        return $timestamp + $expire >= time();
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        if ($userToken = \modules\v1\account\models\GlbUserToken::findOne(['token' => $token])) {
            return static::findOne(['_company_id' => $userToken->company_id, 'user_id' => $userToken->user_id]);
        }
        return null;
    }

    public function getId()
    {
        return $this->getPrimaryKey();
    }

    public function getAuthKey()
    {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }
}