<?php
namespace api\modules\v1\account\models;

use Yii;
use yii\base\Model;

/**
 * Login form
 */
class LoginForm extends Model
{
    public $username;
    public $password;
    public $rememberMe = false;

    private $_glbUser = false;
    private $_fncUser = false;
    private $_tmpUser = false;
    private $_currentRegion;

    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }



    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            if (Yii::$app->strepzConfig->isTempUser === true) {
                $user = $this->getTmpUser();
            } else {
                $user = $this->getUser();
            }
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect username or password.');
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return boolean whether the user is logged in successfully
     */
    public function login($company_id, $user_id)
    {
        if ($this->validate()) {
            // Sets all dependencies for temp users
            return $tokens = GlbUserToken::getToken($company_id, $user_id);
        } else {
            return false;
        }
    }

    public function logout()
    {
        return Yii::$app->user->logout();
    }

    public function getUser()
    {
        $this->_glbUser = GlbUser::getUserData($this->username);
        if ($this->_glbUser !== false) {
            $company_id = $this->_glbUser['company']->company_id;
            $db_linked = $this->_glbUser['company']->db;

            Yii::$app->session->set('fnc_db', $db_linked);
            Yii::$app->session->set('company_id', $company_id);

            Yii::$app->strepzConfig->reloadCompanyId();
            if ($this->_fncUser === false) {
                $this->_fncUser = FncUser::findByUsername($this->username);
            }
        }
        
        return $this->_fncUser;
    }

    // public function registrationAutoLogin($username)
    // {
    //     $this->username = $username;
    //     Yii::$app->session->set('user_temp_mode', false);
    //     if (!Yii::$app->user->isGuest) {
    //         return true;
    //     } else {
    //         return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);    
    //     }        
    // }

    // TEMPORARY USER STUFF
    public function tmpLogin($company_id, $user_id)
    {
        if ($this->validate()) {
            // Sets all dependencies for temp users
            return $tokens = GlbUserToken::getToken($company_id, $user_id);
        } else {
            return false;
        }
    }

    public function getTmpUser()
    {
        $this->_glbUser = GlbUser::getUserData($this->username);
        if ($this->_glbUser !== false) {
            $this->_tmpUser = TmpUser::findOne(['username' => $this->username]);
        }
        return $this->_tmpUser;
    }

    // public function registrationTmpUserAutoLogin($username)
    // {
    //     $this->username = $username;
    //     // VERY IMPORTANT TO USE TEMPORARY DB FOR LOGGING
    //     Yii::$app->user->identityClass = 'api\modules\v1\account\models\TmpUser';
    //     Yii::$app->session->set('user_temp_mode', true);
    //     return Yii::$app->user->login($this->getTmpUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
    // }

    public function attributeLabels()
    {
        return [
            'rememberMe' => 'Remember Me',
            'username' => 'Username',
            'password' => 'Password',
        ];
    }

}