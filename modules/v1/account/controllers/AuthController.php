<?php 

namespace api\modules\v1\account\controllers;

use Yii;
use yii\rest\Controller;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\QueryParamAuth;
use yii\rest\ActiveController;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use api\modules\v1\account\components\authclient\StrepzHttpBearerAuth;
use api\modules\v1\account\models\auth\AuthClient;
use api\modules\v1\account\models\LoginForm;
use api\modules\v1\account\models\SignupForm;
use api\modules\v1\account\models\FncSignupForm;
use api\modules\v1\account\models\GlbUser;
use api\modules\v1\account\models\TmpUser;
use api\modules\v1\account\models\PasswordResetRequestForm;
use api\modules\v1\account\models\ResetPasswordForm;

class AuthController extends Controller
{
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
                'except' => ['options', 'login', 'csrf'],
                'authMethods' => [
                    HttpBasicAuth::className(),
                    StrepzHttpBearerAuth::className(),
                    QueryParamAuth::className(),
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['login', 'csrf'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => [],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['login', 'csrf'],
                        'roles' => ['?'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => \yii\filters\VerbFilter::className(),
                'actions' => [
                    'login' => ['post'],
                    'csrf' => ['get','head'],
                ],
            ],
        ]);
    }

	/**
     * Get CSRF token for login
     * @return string the CSRF token
     */
    public function actionCsrf()
    {
        return Yii::$app->restTemplate->success(Yii::$app->request->getCsrfToken());
    }

 	/**
     * Login method for users
     * @method POST | username, password
     */
    public function actionLogin()
    {
        $currentRegion = strtolower(Yii::$app->params['app_region']);
        if (!$currentRegion) {
            return 'APPLICATION IS BROKEN! LOL!';
        }
        $model = new LoginForm();
        $data = [];
        
        if (Yii::$app->request->post()) {
            $data['LoginForm'] = Yii::$app->request->post();
        }

        if ($model->load($data)) {
            if($user = \api\modules\v1\account\models\GlbUser::getUserData($model->username)) {
                Yii::$app->config->setCompanyId($user->company_id);
                $userRegion = $user['company']->region;
                $userStatus = $user->status;
                // Workaround for unverified users
                if ($userStatus !== GlbUser::STATUS_ACTIVE) {
                    Yii::$app->config->setIsTempUser($userStatus);
                    if ($token = $model->tmpLogin($user->company_id, $user->user_id)) {
                        return Yii::$app->restTemplate->success([
                            'type' => 'Bearer',
                            'token' => $token
                        ]);
                    }
                }
                // Requires strict refactoring
                if ($token = $model->tmpLogin($user->company_id, $user->user_id)) {
                    if ($userRegion === $currentRegion) {
                        // return $this->goBack();
                        \api\modules\v1\account\models\FncConfig::selectProject(0);
                        return Yii::$app->restTemplate->success([
                            'type' => 'Bearer',
                            'token' => $token
                        ]);
                    } else {
                        if ($this->_auth_key = $model->getUser()->auth_key) {
                            $this->_username = $model->getUser()->username;
                            Yii::$app->user->logout();
                            // return $this->redirect(Yii::$app->params[$userRegion . '_domain'] . Url::to(['site/login-auth', 'auth_key' => $this->_auth_key, 'username' => $this->_username]));
                            \api\modules\v1\account\models\FncConfig::selectProject(0);
                            return true;
                        }
                    }
                }
            }
            // This is just so the error shows up as it validates the user
            $model->addError('password', 'Incorrect username or password.');
        }
        return Yii::$app->restTemplate->fail($model->validate() ? true : $model->getErrors(), 403, 'Forbidden');
    }
}