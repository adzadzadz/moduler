<?php 

namespace api\modules\v1\account\controllers;

use Yii;
use api\modules\v1\account\models\auth\AuthClient;
use api\modules\v1\account\models\LoginForm;

class AuthController extends \yii\web\Controller
{
	public function init()
	{
		parent::init();
		$this->layout = 'main';
		$this->viewPath = Yii::getAlias('@accountView');
	}

	public function actionIndex()
	{
		if (!Yii::$app->user->isGuest) {
			return false;
		}

		$model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return true;
        } else {
            return $this->render('auth/index', [
                'model' => $model,
            ]);
        }
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
        if (!Yii::$app->request->post()) {
            // $data = [
            //     'LoginForm' => [
            //         'username' => $username,
            //         'password' => $password
            //     ]
            // ];
            return false;
        } else {
            $data['LoginForm'] = Yii::$app->request->post();
        }
        if ($model->load($data)) {
            if($user = \api\modules\v1\account\models\GlbUser::getUserData($model->username)) {
                Yii::$app->strepzConfig->setCompanyId($user->company_id);
                $userRegion = $user['company']->region;
                $userStatus = $user->status;
                // Workaround for unverified users
                if ($userStatus !== GlbUser::STATUS_ACTIVE) {
                    Yii::$app->strepzConfig->setIsTempUser($userStatus);
                    if ($token = $model->tmpLogin($user->company_id, $user->user_id)) {
                        return [
                            'error' => null,
                            'content' => [
                                'type' => 'Bearer',
                                'token' => $token
                            ]
                        ];
                    }
                }
                // Requires strict refactoring
                if ($token = $model->tmpLogin($user->company_id, $user->user_id)) {
                    if ($userRegion === $currentRegion) {
                        // return $this->goBack();
                        \api\modules\v1\account\models\FncConfig::selectProject(0);
                        return [
                            'error' => null,
                            'content' => [
                                'type' => 'Bearer',
                                'token' => $token
                            ]
                        ];
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
        return ['error' => $model->getErrors()];
    }

}