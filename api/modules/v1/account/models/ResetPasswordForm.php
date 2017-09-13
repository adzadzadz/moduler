<?php
namespace api\modules\v1\account\models;

use api\modules\v1\account\models\FncUser as User;
use yii\base\InvalidParamException;
use yii\base\Model;
use Yii;

/**
 * Password reset form
 */
class ResetPasswordForm extends Model
{
    public $password;

    /**
     * @var \common\models\User
     */
    private $_user;


    /**
     * Creates a form model given a token.
     *
     * @param  string                          $token
     * @param  array                           $config name-value pairs that will be used to initialize the object properties
     * @throws \yii\base\InvalidParamException if token is empty or not valid
     */
    // public function __construct($token, $username, $config = [])
    // {
    //     if (empty($token) || !is_string($token)) {
    //         throw new InvalidParamException('Password reset token cannot be blank.');
    //     }
    //     $this->_user = null;
    //     $glbUser = GlbUser::getUserData($username);
    //     $glbUserCompany = $glbUser->company;

    //     if ($glbUser->status === GlbUser::STATUS_UNVERIFIED || $glbUser->status === GlbUser::STATUS_VERIFIED) {
    //         $user = new TmpUser;
    //         $user = $user->getUser($username);
    //         if ($user->password_reset_token === $token) {
    //             $this->_user = $user;
    //         }
            
    //     } elseif ($glbUser->status === GlbUser::STATUS_ACTIVE) {
    //         Yii::$app->strepzDbManager->setDbId($glbUserCompany->db);
    //         $user = new User;
    //         $user = $user->getUser($username);
    //         if ($user->password_reset_token === $token) {
    //             $this->_user = $user;
    //         }
    //     }

    //     if (!$this->_user) {
    //         throw new InvalidParamException('Invalid token.');
    //     }
    //     parent::__construct($config);
    // }

    public function verifyToken($token, $username)
    {
        if (empty($token) || !is_string($token)) {
            return false;
        }
        $this->_user = null;
        $glbUser = GlbUser::getUserData($username);
        $glbUserCompany = $glbUser->company;

        if ($glbUser->status === GlbUser::STATUS_UNVERIFIED || $glbUser->status === GlbUser::STATUS_VERIFIED) {
            $user = new TmpUser;
            $user = $user->getUser($username);
            if ($user->password_reset_token === $token) {
                $this->_user = $user;
            }
        } elseif ($glbUser->status === GlbUser::STATUS_ACTIVE) {
            Yii::$app->strepzDbManager->setDbId($glbUserCompany->db);
            $user = new User;
            $user = $user->getUser($username);
            if ($user->password_reset_token === $token) {
                $this->_user = $user;
            }
        }

        if (!$this->_user) {
            return false;
        }
        return true;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['password', 'required'],
            ['password', 'string', 'min' => 8],
        ];
    }

    /**
     * Resets password.
     *
     * @return boolean if password was reset.
     */
    public function resetPassword()
    {
        $user = $this->_user;
        $user->setPassword($this->password);
        $user->removePasswordResetToken();

        return $user->save(false);
    }
}
