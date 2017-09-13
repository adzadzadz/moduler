<?php

namespace api\modules\v1\main\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\ForbiddenHttpException;
use frontend\models\SignupForm;
use frontend\models\FncSignupForm;
use frontend\models\GlbUser;
use frontend\models\TmpUser;
use common\models\replication\DbDataReplicator;
use common\models\LoginForm;
use common\models\Company;
use common\models\GlbCompany;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/**
 * Default controller for the `project` module
 */
class DefaultController extends ActiveController
{
    public $modelClass = 'common\models\FncUser';
    private $_currentRegion;
    private $_auth_key;
    private $_username;

    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'corsFilter' => [
                'class' => Yii::$app->strepzCorsFilter->className(),
            ],
            'access' => [
                'class' => AccessControl::className(),
                // 'denyCallback' => function ($rule, $action) {
                //     return $this->redirect(Yii::$app->strepzFeFrnt->loginUrl);
                // },
                // 'only' => ['get-user', 'add', 'view', 'create', 'options'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['get-user', 'add', 'view', 'create', 'options'],
                        'roles' => ['superadmin'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['index'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['get-csrf', 'login', 'signup'],
                        'roles' => ['?'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'get-csrf' => ['POST']
                ],
            ],
        ]);
    }

    public function actions()
    {
        $actions = parent::actions();

        // disable the "delete" and "create" actions
        unset($actions['update'], $actions['delete'], $actions['view'], $actions['create'], $actions['index']);

        // $actions['auth'] = [
        //     'class' => 'yii\authclient\AuthAction',
        //     'successCallback' => [$this, 'onAuthSuccess'],
        // ];

        return $actions;
    }

    public function actionGetCsrf()
    {   
        return Yii::$app->request->getCsrfToken();
    }

    public function actionLanguages()
    {
        return [
            'usage' => '/lang/{{language}} e.g /lang/en-US',
            'languages' => [
                'English' => 'en-US',
                'Dutch'   => 'nl-NL'
            ]
        ];
    }

    public function actionIsAuthorized()
    {
        if (Yii::$app->user->isGuest) {
            return false;
        }
        return true;
    }

    public function actionSetLanguage($language)
    {
        $cookie_name = "lang";
        $cookie_value = $language;
        setcookie($cookie_name, $cookie_value, time() + (86400 * 30), "/"); // 86400 = 1 day
        return [
            'success' => true,
            'language' => $language
        ];
    }

    // public function actionSignup($email = null, $password = null, $confirmpassword = null)
    // {
    //     $model = new SignupForm();

    //     // Used for GET method signup
    //     $data = [];
    //     if (!Yii::$app->request->post()) {
    //         $data = [
    //             'SignupForm' => [
    //                 'email' => $email,
    //                 'username' => $email,
    //                 'password' => $password,
    //                 'confirmpassword' => $confirmpassword
    //             ] 
    //         ];
    //     } else {
    //         $data['SignupForm'] = Yii::$app->request->post();
    //         // Username is the email address 
    //         $data['SignupForm']['username'] = $data['SignupForm']['email'];
    //     }
    //     // return var_dump($data);
    //     if ($model->load($data)) {
    //         if ($user = $model->tmpSignup()) {
    //             if (SignupForm::sendVerificationEmail($user->username, $user->email, $user->firstname . ' ' . $user->lastname, $user->verification_code)) {
    //                 // Fetch user token from global user_token table
    //                 $userToken = \common\models\GlbUserToken::getToken($user->_company_id, $user->user_id);
    //                 return [
    //                     'token' => $userToken
    //                 ];
    //             }
    //             return "it's actually a verification email error";
    //         }
    //     }

    //     return $model->getErrors();
    // }

    // public function actionLogin($username = null, $password = null)
    // {
    //     $this->_currentRegion = strtolower(Yii::$app->params['app_region']);
    //     $model = new LoginForm();
    //     $data = [];
    //     if (!Yii::$app->request->post()) {
    //         $data = [
    //             'LoginForm' => [
    //                 'username' => $username,
    //                 'password' => $password
    //             ]
    //         ];
    //     } else {
    //         $data['LoginForm'] = Yii::$app->request->post();
    //     }
    //     if ($model->load($data)) {
    //         if($user = \common\models\GlbUser::getUserData($model->username)) {
    //             Yii::$app->strepzConfig->setCompanyId($user[0]->company_id);
    //             $userRegion = $user[0]['company'][0]->region;
    //             $userStatus = $user[0]->status;
    //             // Workaround for unverified users
    //             if ($userStatus === GlbUser::STATUS_UNVERIFIED) {
    //                 Yii::$app->strepzConfig->setIsTempUser($userStatus);
    //                 if ($token = $model->tmpLogin($user[0]->company_id, $user[0]->user_id)) {
    //                     return [
    //                         'error' => null,
    //                         'content' => [
    //                             'type' => 'Bearer',
    //                             'token' => $token
    //                         ]
    //                     ];
    //                 }
    //             } elseif ($userStatus === GlbUser::STATUS_VERIFIED) {
    //                 if ($model->tmpLogin()) {
    //                     return true;
    //                 }
    //             }
    //             // Requires strict refactoring
    //             if ($model->login()) {
    //                 // return $userRegion === $this->_currentRegion;
    //                 // return var_dump(Yii::$app->user->isGuest);
    //                 if ($userRegion === $this->_currentRegion) {
    //                     // return $this->goBack();
    //                     \common\models\FncConfig::selectProject(0);
    //                     return true;
    //                 } else {
    //                     if ($this->_auth_key = $model->getUser()->auth_key) {
    //                         $this->_username = $model->getUser()->username;
    //                         Yii::$app->user->logout();
    //                         // return $this->redirect(Yii::$app->params[$userRegion . '_domain'] . Url::to(['site/login-auth', 'auth_key' => $this->_auth_key, 'username' => $this->_username]));
    //                         \common\models\FncConfig::selectProject(0);
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

    // ID is actually the username
    public function actionAutoActivator($verifyer = null, $id = null)
    {
        if ($verifyer !== null && $id !== null) {
            $user = new TmpUser();
            $userData = $user->getUser($id);

            $glbUser = GlbUser::getUserData($userData->username);
            $glbCompany = $glbUser[0]['company'][0];
            
            if ( $userData->status !== TmpUser::STATUS_ACTIVE ) {
                if ( Yii::$app->security->validatePassword($userData->verification_code, $verifyer)  ) {
                
                    $glbUser[0]->status = TmpUser::STATUS_VERIFIED;
                    $userData->status = TmpUser::STATUS_VERIFIED;

                    if ($userData->save() && $glbUser[0]->save()) {
                        return $this->redirect(Yii::$app->params[$userData->_region . '_domain'] . Url::to(['registration/finalize', 'id' => $userData->username, 'token' => $userData->_registration_token, 'method' => 'email']));
                    }
                } else {
                    // return 'fail';
                }
            } else {
                // return 'done';
            }
        } else {
            // return 'sad';
        }
        throw new \yii\web\NotFoundHttpException('Page not found', 404);
    }

    public function actionLogout()
    {
        if (Yii::$app->user->isGuest) {
            throw new \yii\web\NotFoundHttpException('Page not found', 404);
        }
        return Yii::$app->user->logout();
    }
}
