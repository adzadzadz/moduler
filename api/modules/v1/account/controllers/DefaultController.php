<?php

namespace api\modules\v1\account\controllers;

use Yii;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\QueryParamAuth;
use yii\rest\ActiveController;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use common\components\authclient\StrepzHttpBearerAuth;
use api\modules\v1\account\models\LoginForm;
use api\modules\v1\account\models\SignupForm;
use api\modules\v1\account\models\FncSignupForm;
use api\modules\v1\account\models\GlbUser;
use api\modules\v1\account\models\TmpUser;
use api\modules\v1\account\models\PasswordResetRequestForm;
use api\modules\v1\account\models\ResetPasswordForm;


/**
 * Default controller for the `project` module
 */
class DefaultController extends ActiveController
{
    public $modelClass = 'api\modules\v1\account\models\FncUser';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $auth = $behaviors['authenticator'];
        unset($behaviors['authenticator']);

        return ArrayHelper::merge($behaviors, [
            'corsFilter' => [
                'class' => Yii::$app->strepzCorsFilter->className(),
            ],
            'authenticator' => [
                'class' => CompositeAuth::className(),
                'except' => ['options', 'login', 'signup', 'request-password-reset', 'verify-reset-token', 'reset-password'],
                'authMethods' => [
                    HttpBasicAuth::className(),
                    StrepzHttpBearerAuth::className(),
                    QueryParamAuth::className(),
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'only' => [
                    'update-info', 
                    'login', 
                    'signup', 
                    'build', 
                    'request-password-reset', 
                    'verify-reset-token', 
                    'reset-password'
                ],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['update-info', 'build'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => true,
                        'actions' => [
                            'login', 
                            'signup', 
                            'request-password-reset', 
                            'verify-reset-token', 
                            'reset-password'
                        ],
                        'roles' => ['?'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => \yii\filters\VerbFilter::className(),
                'actions' => [
                    'request-password-reset'  => ['post'],
                    'login'   => ['post', 'get'],
                    'signup' => ['post'],
                    'verify-reset-token' => ['post']
                ],
            ],
        ]);
    }

    public function actions()
    {
        $actions = parent::actions();

        // disable the "delete" and "create" actions
        unset($actions['index'], $actions['delete'], $actions['view'], $actions['create'], $actions['update']);
        return $actions;
    }

    /**
     * Signup method for new users
     * @method POST | email, password, confirmpassword
     */
    public function actionSignup()
    {
        $model = new SignupForm();
        $data = [];
        if (isset(Yii::$app->request->post()['email'])) {
            $data['SignupForm'] = Yii::$app->request->post();
            // Username is the email address 
            $data['SignupForm']['username'] = $data['SignupForm']['email'];
        }

        // force validate to show errors if load data fails
        $model->validate();

        if ($model->load($data)) {
            if ($user = $model->tmpSignup()) {
                if (SignupForm::sendVerificationEmail($user->username, $user->email, $user->firstname . ' ' . $user->lastname, $user->verification_code)) {
                    // Fetch user token from global user_token table
                    $token = \api\modules\v1\account\models\GlbUserToken::getToken($user->_company_id, $user->user_id);
                    return [
                            'error' => null,
                            'content' => [
                                'type' => 'Bearer',
                                'token' => $token
                            ]
                        ];
                }
                return "it's actually a verification email error";
            }
        }
        return ['error' => $model->getErrors()];
    }

    /**
     * Login method for users
     * @method POST | username, password
     */
    // public function actionLogin()
    // {
    //     $currentRegion = strtolower(Yii::$app->params['app_region']);
    //     if (!$currentRegion) {
    //         return 'APPLICATION IS BROKEN! LOL!';
    //     }
    //     $model = new LoginForm();
    //     $data = [];
    //     if (!Yii::$app->request->post()) {
    //         // $data = [
    //         //     'LoginForm' => [
    //         //         'username' => $username,
    //         //         'password' => $password
    //         //     ]
    //         // ];
    //         return false;
    //     } else {
    //         $data['LoginForm'] = Yii::$app->request->post();
    //     }
    //     if ($model->load($data)) {
    //         if($user = \api\modules\v1\account\models\GlbUser::getUserData($model->username)) {
    //             Yii::$app->strepzConfig->setCompanyId($user->company_id);
    //             $userRegion = $user['company']->region;
    //             $userStatus = $user->status;
    //             // Workaround for unverified users
    //             if ($userStatus !== GlbUser::STATUS_ACTIVE) {
    //                 Yii::$app->strepzConfig->setIsTempUser($userStatus);
    //                 if ($token = $model->tmpLogin($user->company_id, $user->user_id)) {
    //                     return [
    //                         'success' => true,
    //                         'content' => [
    //                             'type' => 'Bearer',
    //                             'token' => $token
    //                         ]
    //                     ];
    //                 }
    //             }
    //             // Requires strict refactoring
    //             if ($token = $model->tmpLogin($user->company_id, $user->user_id)) {
    //                 if ($userRegion === $currentRegion) {
    //                     // return $this->goBack();
    //                     \api\modules\v1\account\models\FncConfig::selectProject(0);
    //                     return [
    //                         'success' => true,
    //                         'content' => [
    //                             'type' => 'Bearer',
    //                             'token' => $token
    //                         ]
    //                     ];
    //                 } else {
    //                     if ($this->_auth_key = $model->getUser()->auth_key) {
    //                         $this->_username = $model->getUser()->username;
    //                         Yii::$app->user->logout();
    //                         // return $this->redirect(Yii::$app->params[$userRegion . '_domain'] . Url::to(['site/login-auth', 'auth_key' => $this->_auth_key, 'username' => $this->_username]));
    //                         \api\modules\v1\account\models\FncConfig::selectProject(0);
    //                         return true;
    //                     }
    //                 }
    //             }
    //         }
    //         // This is just so the error shows up as it validates the user
    //         $model->addError('password', 'Incorrect username or password.');
    //     }
    //     return ['error' => $model->getErrors()];
    // }

    /**
     * PLEASE BE INFORMED THAT THE ID (at least in this case) IS ACTUALLY THE USERNAME/EMAIL Y_Y
     * e.g myname@strepz.com
     */
    public function actionBuild($id = null, $token = null, $method = null)
    {
        // Avoiding execution time error. 
        set_time_limit ( 360 );

        if (Yii::$app->user->isGuest || Yii::$app->user->identity->status < 6) {
            if ($id === null || $token === null) {
                throw new ForbiddenHttpException('You are not allowed to perform this action.');
            }
        } else {
            $id = Yii::$app->user->identity->username;
        }

        $currentRegion = strtolower(Yii::$app->params['app_region']);
        $glbUser = GlbUser::getUserData($id);
        $glbUser =  $glbUser['company'];
        Yii::$app->strepzConfig->setCompanyId($glbUser->company_id);

        if ($currentRegion === $glbUser->region) {
            $tmpUser = new TmpUser;
            
            if (Yii::$app->user->isGuest && $token !== null) {
                $user = $tmpUser->findUserByRegistrationToken($id, $token);
            } else {
                $user = TmpUser::findOne(Yii::$app->user->id);
            }

            if ($user) {
                $signup = new SignupForm;
                if ($setUser = $signup->signup($user->id)) {
                    Yii::$app->strepzConfig->setIsTempUser($setUser->status);

                    if ($method == 'email') {
                        return $this->redirect('/');
                    } else {
                        return true;
                    }
                }
            }
        } else {
            // REDIRECTION URL WITH ID AND TOKEN AS PARAMS if region does not match
            $token = Yii::$app->user->identity->_registration_token;
            $this->redirect(Yii::$app->params[$glbUser->region . '_domain'] . Url::to(['registration/finalize', 'id' => $id, 'token' => $token]));
        }
        throw new ForbiddenHttpException('You are not allowed to perform this action.');
    }


    /**
     * @string type : "user" or "company"
     */
    public function actionUpdateInfo($type)
    {
        if (Yii::$app->request->post()) {
            if ($type == 'user') {
                $model = \api\modules\v1\account\models\TmpUser::findOne(Yii::$app->user->id);
            } elseif ($type == 'company') {
                $model = \api\modules\v1\account\models\TmpCompany::findOne(1);
            } else {
                throw new \yii\web\NotFoundHttpException('Page not found', 404);
            }

            $key = Yii::$app->request->post()['key'];
            $value = Yii::$app->request->post()['value'];

            $model->$key = $value;

            if ($model->save()) {
                return true;
            }
            return false;
        }
        return false;
    }

    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        $data = [];
        if (Yii::$app->request->post()) {
            $data = [
                'PasswordResetRequestForm' => [
                    'username' => Yii::$app->request->post()['PasswordResetRequestForm']['username'],
                    'email'    => Yii::$app->request->post()['PasswordResetRequestForm']['username'],
                ]
            ];
        }

        if ($model->load($data) && $model->validate()) {
            if ($model->sendEmail()) {
                return true;
            } else {

            }
        }

        return false;
    }

    public function actionResetPassword($token, $u)
    {
        $model = new ResetPasswordForm;

        if ($model->verifyToken($token, $u) && $model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            return true;
        }

        return false;
    }

    /**
     * Verifies if the reset token is linked to the email account
     * @param  string $token the password reset token value
     * @param  string $email the email associated with the token
     * @return boolean  Final say if the reset token is verified or not
     */
    public function actionVerifyResetToken($token, $email)
    {
        $model = new ResetPasswordForm;

        if ($model->verifyToken($token, $email)) {
            return true;
        }
        return false;
    }
}