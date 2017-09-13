<?php
namespace api\modules\v1\account\models;

use Yii;
use api\modules\v1\account\models\FncUser as User;
use yii\base\Model;
use common\models\GlbUser;

/**
 * Password reset request form
 */
class PasswordResetRequestForm extends Model
{
    public $email;
    public $username;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'email'], 'filter', 'filter' => 'trim'],
            [['username'], 'required'],
            [['username'], 'email'],
            ['username', 'exist',
                'targetClass' => '\common\models\GlbUser',
                // 'filter' => ['status' => User::STATUS_ACTIVE],
                'message' => 'There is no user with such email.'
            ],
        ];
    }

    // public function rules()
    // {
    //     return [
    //         ['email', 'filter', 'filter' => 'trim'],
    //         ['email', 'required'],
    //         ['email', 'email'],
    //         ['email', 'exist',
    //             'targetClass' => '\common\models\GlbUser',
    //             // 'filter' => ['status' => User::STATUS_ACTIVE],
    //             'message' => 'There is no user with such email.'
    //         ],
    //     ];
    // }

    /**
     * Sends an email with a link, for resetting the password.
     *
     * @return boolean whether the email was send
     */
    public function sendEmail()
    {
        $user = false;
        $glbUser = GlbUser::getUserData($this->username);
        $glbUserCompany = $glbUser->company;

        if ($glbUser->status === GlbUser::STATUS_UNVERIFIED || $glbUser->status === GlbUser::STATUS_VERIFIED) {
            $user = new TmpUser;
            $user = $user->getUser($this->username);
            
        } elseif ($glbUser->status === GlbUser::STATUS_ACTIVE) {
            // Yii::$app->session->set('fnc_db', $glbUserCompany->db);
            Yii::$app->strepzDbManager->setDbId($glbUserCompany->db);
            $user = new User;
            $user = $user->getUser($this->username);
        }
        
        if ($user) {
            if (!User::isPasswordResetTokenValid($user->password_reset_token)) {
                $user->generatePasswordResetToken();
            }
            // return $user->save();
            if ($user->save()) {
                return Yii::$app->mailer->compose(['html' => 'passwordResetToken-html', 'text' => 'passwordResetToken-text'], ['user' => $user])
                    ->setFrom(['no-reply@strepz.com' => 'Strepz Password Reset'])
                    ->setTo($this->email)
                    ->setSubject('Password reset for your Strepz account')
                    ->send();
            }
        }
        return false;
    }
}