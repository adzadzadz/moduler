<?php
namespace modules\v1\account;

use Yii;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\helpers\ArrayHelper;
use yii\filters\auth\QueryParamAuth;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use common\components\authclient\StrepzHttpBearerAuth;
use modules\v1\account\models\TmpUser;
use modules\v1\account\models\GlbUser;
use modules\v1\account\models\Fetcher;

/**
 * User controller
 * All actions should be strictle limited to ajax access before v1.0
 */
class UserController extends \yii\rest\ActiveController
{
    public $modelClass = 'modules\v1\account\models\FncUser';

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
                'except' => ['options'],
                'authMethods' => [
                    HttpBasicAuth::className(),
                    StrepzHttpBearerAuth::className(),
                    QueryParamAuth::className(),
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['activate', 'get-user', 'add', 'view', 'create', 'get-me'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['get-user', 'add', 'create'],
                        'roles' => ['superadmin'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['get-me', 'index', 'activate', 'view'],
                        'roles' => ['@'],
                    ],
                ],
            ],
        ]);
    }

    public function actions()
    {
        $actions = parent::actions();

        // disable the "delete" and "create" actions
        unset($actions['index'], $actions['delete'], $actions['view'], $actions['create']);
        return $actions;
    }

    /**
     * Fetches all users under the current company if the ID is null
     * @param  integer id
     * @return json
     */
    public function actionIndex()
    {
        $data = [];
        $list = \frontend\models\FncUser::find()
            ->where(['!=', 'id', Yii::$app->user->id])
            ->joinWith('authAssignment')
            ->all();

        // Format
        foreach ($list as $user) {
            $roles = [];
            foreach ($user->authAssignment as $assigned) {
                if ($assigned->project_id === Yii::$app->strepzConfig->selectedProject) {
                    $roles[] = $assigned->item_name;    
                }
            }

            $data[] = [
                'info' => [
                    // 'company_id' => $user->_company_id,
                    'id' => $user->id,
                    'email' => $user->email,
                    'firstname' => $user->firstname,
                    'middlename' => $user->middlename,
                    'lastname' => $user->lastname,
                    'status' => $user->status,
                ],
                'roles' => $roles
            ];
        }

        return $data;
    }

    /**
     * Checks wether the user is a guest
     * Fetches the current user information including it's assigned roles for page controls.
     * @return json
     */
    private function getMe()
    {   
        $userData = [];
        if (Yii::$app->strepzConfig->isTempUser) {
            $user = TmpUser::findOne(Yii::$app->user->id);
            $user = [
                'info' => [
                    'company_id' => $user->_company_id,
                    'region' => $user->_region,
                    'id' => $user->id,
                    'email' => $user->email,
                    'firstname' => $user->firstname,
                    'middlename' => $user->middlename,
                    'lastname' => $user->lastname,
                    'language' => $user->language,
                    'status' => $user->status,
                ],
                'roles' => null,
                'projects' => null,
            ];
        } else {
            $user = Fetcher::getUser(Yii::$app->user->id);
        }
        return [
            'isLogged' => true,
            'isVerified' => !Yii::$app->strepzConfig->isTempUser,
            'user' => $user,
        ];
    }

    /**
     * Fetches all users under the current company if the ID is null
     * @param  integer|string id
     * @return json
     */
    public function actionView($id = null)
    {
        // Verifies if user exists with password reset token and email
        // Usually in use to reset password
        if (Yii::$app->request->post()) {
            if (isset(Yii::$app->request->post()['token']) && isset(Yii::$app->request->post()['email'])) {
                $user = GlbUser::findOne([
                    'password_reset_token' => Yii::$app->request->post()['token'],
                    'email' => Yii::$app->request->post()['email']
                ]);
                if ($user) {
                    return true;
                } else {
                    return false;
                }
            }
        }

        // If no data is passed, 
        if (!Yii::$app->user->isGuest && $id == 'self') {
            return $this->getMe();
        }

        // Business as usual
        // Method requires review
        /* 
        $data = [];
        if (is_null($id)) {
            $list = \frontend\models\FncUser::find()
                ->where(['!=', 'id', Yii::$app->user->id])
                ->joinWith('authAssignment')
                ->all();

            // Format
            foreach ($list as $user) {
                $roles = [];
                foreach ($user->authAssignment as $assigned) {
                    if ($assigned->project_id === Yii::$app->strepzConfig->selectedProject) {
                        $roles[] = $assigned->item_name;    
                    }
                }

                $data[] = [
                    'info' => [
                        // 'company_id' => $user->_company_id,
                        'id' => $user->id,
                        'email' => $user->email,
                        'firstname' => $user->firstname,
                        'middlename' => $user->middlename,
                        'lastname' => $user->lastname,
                        'status' => $user->status,
                    ],
                    'roles' => $roles
                ];
            }
        } else {
            $data = Fetcher::getUser($id);
        }

        return $data;
        */
    }

    /**
     * Adds a user
     * 
     */
    public function actionCreate()
    {
        $model = new \frontend\models\SignupForm;

        if ($updatedPostArray = Yii::$app->request->post()) {
            // Set random password to be sent through email
            $pwd = YII_ENV === 'dev' ? 'Qwerasdf!234' : Yii::$app->security->generateRandomString(8);
            $updatedPostArray['SignupForm']['password'] = $pwd;
        }

        if ($model->load($updatedPostArray) && $user = $model->invite()) {

            $userProject = new \modules\v1\account\models\FncUserProject;

            $userProject->user_id = $user->id;
            $userProject->project_id = Yii::$app->strepzConfig->selectedProject;

            if ($userProject->save() && $model->sendInvitation($user->email, $pwd, $user->firstname . " " . $user->lastname)) {
                return Fetcher::filterUserData($user);
            }
        }
        
        return false;
    }

    /**
     * Manually typed verification is accessed through ajax
     */
    public function actionActivate()
    {   
        if (isset(Yii::$app->request->post()['code'])) {
            $code = Yii::$app->request->post()['code'];
            $user = new TmpUser();
            $userData = $user->getUser();

            $glbUser = GlbUser::getUserData($userData->username);
            $glbCompany = $glbUser[0]['company'][0];
            
            if ( $userData->status !== TmpUser::STATUS_ACTIVE ) {
                if ($code === $userData->verification_code) {
                
                    $glbUser[0]->status = TmpUser::STATUS_VERIFIED;
                    $userData->status = TmpUser::STATUS_VERIFIED;

                    if ($userData->save() && $glbUser[0]->save()) {
                        return true;
                    }
                }
            } else {
                return 'done';
            }
        }
        return false;
        // throw new \yii\web\NotFoundHttpException('Page not found', 404);
    }

}