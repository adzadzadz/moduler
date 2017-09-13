<?php
namespace modules\v1\account\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\base\Model;
use yii\web\Session;
use modules\v1\account\models\GlbUser;
use modules\v1\account\models\TmpUser;
use common\models\Fnc;
use common\models\FncCompany as Company;
use common\models\FncConfig as Config;
use common\models\FncLogs as Logs;
use common\models\FncUser as User;
use common\models\TmpCompany;
use common\models\GlbCompany;
use common\models\GlbUserToken;

class SignupForm extends ActiveRecord
{
    const USER_ID_DEFAULT = 1; 

    public $username;
    public $email;
    public $company_id;
    public $region;
    public $status;

    public $password = '';
    public $confirmpassword = '';

    public $password_hash;
    public $auth_key;
    public $ipaddress;

    public $firstname;
    public $middlename;
    public $lastname;
    public $mobile = 0;
    public $phone = 0;
    public $role;
    public $created_at;
    public $updated_at;
    public $company_name;

    public $verification;

    /**
     * @inheritdoc
     */

    // public static function getDb()
    // {
    //     return Yii::$app->fnc_db_01;
    // }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    public static function checkValid($id, $token)
    {
        if ($id !== null && $token !== null) {
            return true;
        }
        return false;
    }

    public function rules()
    {
        return [
            ['username', 'filter', 'filter' => 'trim'],
            ['username', 'required'],
            ['username', 'email'],
            ['username', 'unique', 'targetClass' => '\common\models\GlbUser', 'message' => 'This username has already been taken.'],
            ['username', 'string', 'min' => 2, 'max' => 255],

            // ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'unique', 'targetClass' => '\common\models\FncUser', 'message' => 'This email address has already been taken.'],

            ['region', 'default', 'value' => 'eu'],

            [['password', 'confirmpassword'], 'required'],
            [['password', 'confirmpassword'],'string', 'min' => 6, 'max' => 255, 'message' => 'Password too short.'],
            ['password', 'compare', 'compareAttribute' => 'confirmpassword', 'message' => 'Password does not match.'],

            // // UserMeta
            // [['firstname', 'lastname', 'region'], 'required'],
            ['status', 'default', 'value' => GlbCompany::STATUS_UNVERIFIED],
            [['company_id', 'phone', 'mobile', 'status', 'created_at', 'updated_at'], 'integer'],
            [['firstname', 'middlename', 'lastname', 'email', 'role'], 'string', 'max' => 255],

            // // Step2 area
            // // [['verification_no', 'region', 'company_name'], 'required'],
            // [['verification_no', 'region', 'company_name'], 'string', 'max' => 255],
        ];
    }

    // public function authSignup()
    // {
    //     if ($this->validate()) {
    //         $user = new User;
    //         $user->username = $this->email;
    //         $user->email = $this->email;
    //         $user->setPassword($this->password);
    //         $user->generateAuthKey();
    //         $user->generatePasswordResetToken();

    //         if ($user->save() && $this->setInitRole($user, false)) {
    //             return $user;
    //         }
    //     }

    //     return false;
    // }

    // Data is stored to glb_reg_db_01 for verification
    public function tmpSignup()
    {
        if ($this->validate(['username', 'region', 'password', 'confirmpassword', 'firstname', 'middlename', 'lastname', 'role', 'company_id', 'phone', 'mobile', 'status'])) {

            if ($this->initTmpTables()) {
                
                $glbUser = new GlbUser;
                $glbUser->username = $this->username;
                $glbUser->user_id = self::USER_ID_DEFAULT;
                $glbUser->company_id = $this->company_id;
                $glbUser->type = GlbUser::TYPE_OWNER;

                $glbCompany = new GlbCompany;
                $glbCompany->company_id = $this->company_id;
                $glbCompany->region = $this->region;
                $glbCompany->ipaddress = Yii::$app->getRequest()->getUserIP();

                $glbUserToken = new GlbUserToken;
                $glbUserToken->company_id = $this->company_id;
                $glbUserToken->user_id = self::USER_ID_DEFAULT;
                $glbUserToken->token = GLbUserToken::generateUniqueToken();

                $replicateData = [
                    'user' => [
                        'company_id' => $this->company_id,
                        'username'   => $this->username,
                    ],
                    'company' => [
                        'company_id' => $this->company_id,
                        'region'     => $this->region,
                        'ipaddress'  => Yii::$app->getRequest()->getUserIP(),
                    ],
                ];

                // if ($glbUser->save() && $glbCompany->save() && Yii::$app->db_rep->replicateGlobalData($replicateData)) {
                if ($glbUser->save() && $glbCompany->save() && $glbUserToken->save()) {
                    $tmpUser = new TmpUser;
                    $tmpUser->_company_id = $this->company_id;
                    $tmpUser->_registration_token = Yii::$app->security->generateRandomString(20);
                    $tmpUser->_region = $this->region;
                    $tmpUser->_ipaddress = Yii::$app->getRequest()->getUserIP();
                    $tmpUser->_access_token = Yii::$app->security->generateRandomString(30);
                    $tmpUser->setPassword($this->password);
                    $tmpUser->generateAuthKey();
                    $tmpUser->username = $this->username;
                    $tmpUser->email = $this->email;
                    $tmpUser->language = 'en-US';
                    $tmpUser->verification_code = Yii::$app->security->generateRandomString(15);

                    $tmpCompany = new TmpCompany;
                    $tmpCompany->name = '';
                    $tmpCompany->address = '';
                    $tmpCompany->postal_code = '';
                    $tmpCompany->city = '';
                    $tmpCompany->state = '';
                    $tmpCompany->country = '';
                    $tmpCompany->phone = '';
                    $tmpCompany->fax = '';
                    $tmpCompany->email = '';
                    $tmpCompany->website = '';
                    $tmpCompany->type = '';
                    $tmpCompany->size = '';
   
                    if ($tmpUser->save() && $tmpCompany->save()) {
                        return $tmpUser;
                    }
                }
            }
        }
        
        return false;
    }

    public function signup($id)
    {
        $tmpUser = TmpUser::findOne($id);
        $this->company_id = $tmpUser->_company_id;
        
        // DATABASE SETTER - CURRENTLY STATIC
        $selected_user_db = Yii::$app->strepzDbManager->selectDb();
        if ($selected_user_db === null) {
            return false;
        }
        Yii::$app->session->set('fnc_db', $selected_user_db);
        // DB SETTER - END
        // Essential for the registration process
        Yii::$app->session->set('company_id', $this->company_id);

        if (Fnc::loadTables($this->company_id)) {
            
            // $glbCompany = GlbCompany::findOne(['company_id' => $this->company_id]);
            $glbUser = GlbUser::getUserData($tmpUser->username);

            $user = new User;
            $user->company_id = $this->company_id;
            $user->ipaddress = $tmpUser->_ipaddress;
            $user->username = $tmpUser->username;
            $user->email = $tmpUser->email;
            $user->password_hash = $tmpUser->password_hash;
            $user->auth_key = $tmpUser->auth_key;
            $user->firstname = $tmpUser->firstname;
            $user->middlename = $tmpUser->middlename;
            $user->lastname = $tmpUser->lastname;
            // $user->mobile = $tmpUser->mobile;
            // $user->phone = $tmpUser->phone;
            $user->status = User::STATUS_ACTIVE;
            // $user->role = 'admin';
            $user->verification_code = $tmpUser->verification_code;

            // Finish moving company data
            // finding company_id 1 as currently only one company is registered
            $tmpCompany = TmpCompany::findOne(1);

            $company = new Company;
            $company->id = $tmpCompany->id;
            $company->name = $tmpCompany->name;
            $company->address = $tmpCompany->address;
            $company->postal_code = $tmpCompany->postal_code;
            $company->city = $tmpCompany->city;
            $company->state = $tmpCompany->state;
            $company->country = $tmpCompany->country;
            $company->phone = $tmpCompany->phone;
            $company->fax = $tmpCompany->fax;
            $company->email = $tmpCompany->email;
            $company->website = $tmpCompany->website;

            $company->type  = $tmpCompany->type;
            $company->size  = $tmpCompany->size;

            $company->status  = $tmpCompany->status;

            if ($user->save() && $company->save()) {

                $config = new Config;
                $config->user_id = $user->id;
                $config->type = 'userDefaults';
                $config->name = 'language';
                $config->value = $tmpUser->language;
                $config->save();

                 $replicateData = [
                    'company' => [
                        'company_id' => $this->company_id,
                        'user_id' => $user->id,
                        'db' => $selected_user_db,
                    ],
                ];
                $glbUser[0]->user_id = $user->id;
                $glbUser[0]->status = User::STATUS_ACTIVE;
                $glbCompany = $glbUser[0]['company'][0];
                $glbCompany->db = $selected_user_db;

                if ($glbCompany->save() && $glbUser[0]->save() && Yii::$app->db_rep->replicateGlobalData($replicateData, false)) {
                    // Assign super admim role to the company owner
                    $auth = Yii::$app->getAuthManager();
                    $role = $auth->getRole('superadmin');
                    $auth->assign($role, $user->id);
                    return $user;
                }
            }
        }

        return false;
    }

    public function invite()
    {
        if ($this->validate(['email'])) {
            // Set required data
            $this->company_id = Yii::$app->strepzConfig->company_id;
            $this->username = $this->email;
            $glbUserData = GlbCompany::findOne(['company_id' => $this->company_id]);
            $this->region = $glbUserData->region;

            // Store FNC data first
            $user = new User;
            $user->company_id = $this->company_id;
            $user->ipaddress = Yii::$app->getRequest()->getUserIP();
            $user->username = $this->username;
            $user->email = $this->email;
            $user->setPassword($this->password);
            $user->generateAuthKey();
            $user->firstname = '';
            $user->middlename = '';
            $user->lastname = '';
            // $user->mobile = $tmpUser->mobile;
            // $user->phone = $tmpUser->phone;
            $user->status = User::STATUS_ACTIVE;
            // $user->role = 'admin';
            $user->verification_code = Yii::$app->security->generateRandomString(15);

            if ($user->save()) {

                $config = new Config;
                $config->user_id = $user->id;
                $config->type = 'userDefaults';
                $config->name = 'language';
                $config->value = 'en-US';#$tmpUser->language;
                $config->save();

                $glbUser = new GlbUser;
                $glbUser->username = $user->username;
                $glbUser->company_id = $this->company_id;
                $glbUser->user_id = $user->id;
                $glbUser->type = GlbUser::TYPE_MEMBER;
                $glbUser->status = GlbUser::STATUS_ACTIVE;

                $replicateData = [
                    'user' => [
                        'company_id' => $this->company_id,
                        'username'   => $user->username,
                    ],
                ];

                // if ($glbUser->save() && $glbCompany->save() && Yii::$app->db_rep->replicateGlobalData($replicateData)) {
                if ($glbUser->save()) {
                    return $user;
                }
            }                
        }

        return false;
    }

    private function initTmpTables()
    {
        $tmpTables = new \frontend\migrations\TmpTables([
            'db' => 'glb_reg_db_01'
        ]);
        if ($tmpTables->up()) {
            $this->company_id = $tmpTables->company_id;
        }
        Yii::$app->strepzConfig->setCompanyId($this->company_id);
        return $this->company_id;
    }

    public function getUserByID($id)
    {
        return GlbUser::findOne($id);
    }

    // NEEDS ADJUSTMENT TO WORK ON CURRENT DB DESIGN
    public static function sendVerificationEmail($username, $email, $name, $code)
    {
        $body = '';
        
        return Yii::$app->mailer->compose('layouts/emailverification', ['content' => $body, 'username' => $username, 'code' => $code, 'host' => $_SERVER['HTTP_HOST']])
            ->setTo([$email => $name])
            ->setFrom(['no-reply@strepz.com' => 'Strepz Registration'])
            ->setSubject('Account verification')
            ->send();
    }

    public static function sendInvitation($email, $password, $name)
    {
        $body = '';

        return Yii::$app->mailer->compose('layouts/userInvitation', ['content' => $body, 'email' => $email, 'password' => $password, 'name' => $name])
            ->setTo([$email => $name])
            ->setFrom(['no-reply@strepz.com' => 'Strepz Registration'])
            ->setSubject('Account Invitation')
            ->send();
    }

    public function attributeLabels()
    {
        return [
            'verification' => 'Username',
            'id' => 'ID',
            'username' => 'Username',
            'email' => 'Email',
            'password' => 'Password',
            'confirmpassword' => 'Confirm Password',
            'firstname' => 'First Name',
            'middlename' => 'Middle Name',
            'lastname' => 'Last Name',
            'mobile' => 'Mobile',
            'phone' => 'Phone',
            'role' => 'Role',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'region' => 'Data Location',     
            'verification' => 'Verification Code',
        ];
    }
}