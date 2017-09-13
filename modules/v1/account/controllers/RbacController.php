<?php
namespace modules\v1\account;

use Yii;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use common\components\authclient\StrepzHttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use yii\rest\ActiveController;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\Url;
use modules\v1\account\models\Company;
use modules\v1\account\models\GlbUserCompany;
use modules\v1\account\models\rbac\AuthItem;
use modules\v1\account\models\FncUser;
use yii\helpers\ArrayHelper;

class RbacController extends ActiveController
{
    public $modelClass = 'modules\v1\account\models\rbac\AuthAssignment';

    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'authenticator' => [
                'class' => CompositeAuth::className(),
                'authMethods' => [
                    HttpBasicAuth::className(),
                    StrepzHttpBearerAuth::className(),
                    QueryParamAuth::className(),
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                // 'denyCallback' => function ($rule, $action) {
                //     return $this->redirect(['/guest/is-guest']);
                // },
                'rules' => [
                    [
                        'allow' => true,
                        // 'actions' => ['create-role', 'get-roles', 'update', 'delete', 'index'],
                        'roles' => ['superadmin'],
                    ],
                ],
            ],
        ]);
    }

    public function actions()
    {
        $actions = parent::actions();

        // disable the "delete" and "create" actions
        unset($actions['index'], $actions['delete'], $actions['update']);
        return $actions;
    }

    /**
     * Fetch current company roles set.
     * @return json
     */
    public function actionIndex()
    {
        $roles = Yii::$app->authManager->getRoles();
        $data = [];
        foreach ($roles as $each) {
            $data[] = $each;
        }
        return $data;
    }

    /**
     * Create custom role
     * Set children roles and permissions
     * tentenenentenen
     * @return json
     */
    public function actionCreateRole()
    {
        $name = Yii::$app->request->post()['name'];
        $description = Yii::$app->request->post()['description'];
        $children = [];

        $auth = Yii::$app->authManager;
        $name = $auth->createRole($name);
        $name->description = $description;
        $auth->add($name);

        return $this->redirect(['index']);
    }

    /**
     * Toggle add/delete updates the user role
     */
    public function actionUpdate($id)
    {
        if (isset(Yii::$app->request->post()['role']) && isset(Yii::$app->request->post()['id'])) {
            $userId = Yii::$app->request->post()['id'];
            $roleName = Yii::$app->request->post()['role'];
            // return json_encode([$userId, $roleName]);
            $auth = Yii::$app->authManager;
            $role = $auth->getRole($roleName);
            // Check if user is linked with the role

            if ($userId === Yii::$app->user->id) {
                return false;
            }

            if (!is_null($auth->getAssignment($roleName, $userId))) {
                $revoke = $auth->revoke( $role, $userId );
                $result = [
                    'result' => 'revoked',
                    'userId' => $userId,
                    'roleName' => $roleName
                ];
            } else {
                $assign = $auth->assign($role, $userId);
                $result = [
                    'result' => 'assigned',
                    'userId' => $assign->userId,
                    'roleName' => $assign->roleName
                ];
            }
            return $result;
        }
        return false;
    }

    public function actionDelete()
    {
    	return "delete";
    }

    // public function actionRunPermission()
    // {
    //     $rbac = new \modules\v1\account\models\rbac\Rbac;
    //     $rbac->projectPermissions();

    //     return 'adz';
    // }
}