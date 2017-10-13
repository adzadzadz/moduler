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
use api\modules\v1\account\components\authclient\StrepzHttpBearerAuth;
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
                'except' => ['options', 'signup', 'request-password-reset', 'verify-reset-token', 'reset-password'],
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
                    'signup', 
                    'build', 
                    'request-password-reset', 
                    'verify-reset-token', 
                    'reset-password',
                    'verify'
                ],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['update-info', 'build', 'verify'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => true,
                        'actions' => [
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
                    'request-password-reset'  => ['POST'],
                    'signup' => ['POST'],
                    'verify-reset-token' => ['GET'],
                    'reset-password' => ['PUT', 'PATCH']
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

        if ($model->load($data)) {
            if ($user = $model->tmpSignup()) {
                if (SignupForm::sendVerificationEmail($user->username, $user->email, $user->firstname . ' ' . $user->lastname, $user->verification_code)) {
                    // Fetch user token from global user_token table
                    $token = \api\modules\v1\account\models\GlbUserToken::getToken($user->_company_id, $user->user_id);
                    return Yii::$app->restTemplate->success([
                        'type' => 'Bearer',
                        'token' => $token
                    ]);
                }
            }
        }
        return Yii::$app->restTemplate->fail($model->validate() ? true : $model->getErrors(), 400, 'Bad Request');
    }

    /**
     * Builds user data to the functional databases.
     * This simply means that the user is verified and can now use the app's features
     * @return json the build result
     */
    public function actionBuild()
    {
        // Avoiding execution time error. 
        set_time_limit ( 360 );

        // email/username and token must be present to build user
        if (Yii::$app->user->isGuest || Yii::$app->user->identity->status < 6 || Yii::$app->request->post('token') === null) {
            throw new \yii\web\NotFoundHttpException('Page not found', 404);
        }

        $token = Yii::$app->request->post('token');
        $username = Yii::$app->user->identity->username;

        // Gather essential data to perform build
        $currentRegion = strtolower(Yii::$app->params['app_region']);
        $glbUser = GlbUser::getUserData($username);
        $glbUser =  $glbUser['company'];
        Yii::$app->config->setCompanyId($glbUser->company_id);

        if ($currentRegion === $glbUser->region) {
            $tmpUser = new TmpUser;
            
            if (Yii::$app->user->isGuest && $token) {
                $user = $tmpUser->findUserByRegistrationToken($username, $token);
            } else {
                $user = TmpUser::findOne(Yii::$app->user->id);
            }

            if ($user) {
                $signup = new SignupForm;
                if ($setUser = $signup->signup($user->id)) {
                    Yii::$app->config->setIsTempUser($setUser->status);

                    return Yii::$app->restTemplate->success(['message' => 'User account has been succesfully built.']);
                }
            }
        } else {
            // Regions does not match, present a solution or display error
            return Yii::$app->restTemplate->fail([
                'message' => 'The regions does not match. Please contact the administrator.'
            ], 500, 'Internal Server Error');
        }
        throw new \yii\web\NotFoundHttpException('Page not found', 404);
    }

    /**
     * Verify user's email
     * @return boolean verification result
     */
    public function actionVerify()
    {
        if (isset(Yii::$app->request->post()['code'])) {
            $code = Yii::$app->request->post()['code'];
            $user = new TmpUser();
            $userData = $user->getUser();

            $glbUser = GlbUser::getUserData($userData->username);
            $glbCompany = $glbUser['company'];

            if ( $userData->status < TmpUser::STATUS_VERIFIED ) {
                if ($code === $userData->verification_code) {
                
                    $glbUser->status = TmpUser::STATUS_VERIFIED;
                    $userData->status = TmpUser::STATUS_VERIFIED;

                    if ($userData->save() && $glbUser->save()) {
                        return Yii::$app->restTemplate->success(['message' => 'Account succesfully verified.']);
                    }
                }
            } else {
                return Yii::$app->restTemplate->success(['message' => 'Account is already verified.']);
            }
        }
        throw new \yii\web\NotFoundHttpException('Page not found', 404);
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
                return Yii::$app->restTemplate->success(['message' => 'User information updated']);
            }
            // Save failed
            return Yii::$app->restTemplate->fail(null, 500, 'Internal Server Error');
        }
        throw new \yii\web\NotFoundHttpException('Page not found', 404);
    }

    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        $data = [];
        if (Yii::$app->request->post()) {
            $data = [
                'PasswordResetRequestForm' => [
                    'username' => Yii::$app->request->post()['email'],
                    'email'    => Yii::$app->request->post()['email'],
                ]
            ];
        }

        if ($model->load($data) && $model->validate()) {
            if ($model->sendEmail()) {
                return Yii::$app->restTemplate->success(['message' => 'The password reset link has been sent to your email']);
            } else {
                return Yii::$app->restTemplate->fail(null, 500, 'Internal Server Error');
            }
        }
        return Yii::$app->restTemplate->fail($model->validate() ? null : $model->getErrors(), 400, 'Bad Request');
    }

    public function actionResetPassword($token, $email)
    {
        $model = new ResetPasswordForm;

        if ($model->verifyToken($token, $email) && $model->load(['ResetPasswordForm' => Yii::$app->request->post()]) && $model->validate() && $model->resetPassword()) {
            return Yii::$app->restTemplate->success(['message' => 'The password has been succesfully reset']);
        }
        return Yii::$app->restTemplate->fail($model->validate() ? null : $model->getErrors(), 400, 'Bad Request');
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
            return Yii::$app->restTemplate->success(['message' => 'Token verified']);
        }
        return Yii::$app->restTemplate->fail(null, 400, 'Bad Request');
    }
}