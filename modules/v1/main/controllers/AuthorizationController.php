<?php

namespace api\modules\v1\main\controllers;

use Yii;
use common\components\authclient\OAuth2;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use yii\rest\ActiveController;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\ForbiddenHttpException;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/**
 * Authorization controller for the `project` module
 */
class AuthorizationController extends ActiveController
{
    public $modelClass = 'common\models\FncUser';
   
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'authenticator' => [
                'class' => CompositeAuth::className(),
                'authMethods' => [
                    HttpBasicAuth::className(),
                    HttpBearerAuth::className(),
                    QueryParamAuth::className(),
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                // 'denyCallback' => function ($rule, $action) {
                //     return $this->redirect(['/guest/is-guest']);
                // },
                // 'only' => ['get-user', 'add', 'view', 'create', 'options'],
                // 'rules' => [
                //     [
                //         'allow' => true,
                //         'actions' => ['get-user', 'add', 'view', 'create', 'options'],
                //         'roles' => ['superadmin'],
                //     ],
                //     [
                //         'allow' => true,
                //         'actions' => ['get-me', 'index'],
                //         'roles' => ['@'],
                //     ],
                // ],
            ],
        ]);
    }

    public function actions()
    {
        $actions = parent::actions();

        // disable the "delete" and "create" actions
        unset($actions['update'], $actions['delete'], $actions['view'], $actions['create'], $actions['index']);
        return $actions;
    }

    public function actionAuthorize()
    {
        return 'adz';
    }

}
