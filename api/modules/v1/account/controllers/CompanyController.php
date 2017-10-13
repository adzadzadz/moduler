<?php
namespace api\modules\v1\account\controllers;

use Yii;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\QueryParamAuth;
use common\components\authclient\StrepzHttpBearerAuth;
use yii\rest\ActiveController;
use yii\helpers\ArrayHelper;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * User controller
 */
class CompanyController extends ActiveController
{
    public $modelClass = '';

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
                'only' => ['get-info'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['get-info'],
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
        unset($actions['delete'], $actions['create']);
        return $actions;
    }

    public function actionGetInfo()
    {
        if (!Yii::$app->user->isGuest) {
            if (Yii::$app->config->isTempUser) {
                $company = \api\modules\v1\account\models\TmpCompany::findOne(1);
            } else {
                $company = \api\modules\v1\account\models\FncCompany::findOne(1);
            }
            return [
                'name' => $company->name,
                'address' => $company->address,
                'postal_code' => $company->postal_code,
                'city' => $company->city,
                'state' => $company->state,
                'country' => $company->country,
                'phone' => $company->phone,
                'fax' => $company->fax,
                'email' => $company->email,
                'website' => $company->website,
                'type' => $company->type,
                'size' => $company->size,
                'status' => $company->status,
            ];
        }
        return false;
    }
}