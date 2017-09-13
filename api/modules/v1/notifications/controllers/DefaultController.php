<?php

namespace api\modules\v1\notifications\controllers;

use Yii;
use api\modules\v1\notifications\models\Template;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\helpers\ArrayHelper;
use yii\filters\auth\QueryParamAuth;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use common\components\authclient\StrepzHttpBearerAuth;

/**
 * Default controller for the `main` module
 */
class DefaultController extends \yii\rest\ActiveController
{
    public $modelClass = 'api\modules\v1\notifications\models\Notifications';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $auth = $behaviors['authenticator'];
        unset($behaviors['authenticator']);

        return ArrayHelper::merge($behaviors, [
            'corsFilter' => [
                'class' => Yii::$app->strepzCorsFilter->className(),
            ],

            // 'authenticator' => [
            //     'class' => CompositeAuth::className(),
            //     'except' => ['options'],
            //     'authMethods' => [
            //         HttpBasicAuth::className(),
            //         StrepzHttpBearerAuth::className(),
            //         QueryParamAuth::className(),
            //     ],
            // ],
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['view'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['view'],
                        'roles' => ['?'],
                    ],
                    // [
                    //     'allow' => true,
                    //     'actions' => ['get-me', 'index', 'activate'],
                    //     'roles' => ['@'],
                    // ],
                ],
            ],
        ]);

    }

    public function beforeAction($action)
    {
        // your custom code here, if you want the code to run before action filters,
        // which are triggered on the [[EVENT_BEFORE_ACTION]] event, e.g. PageCache or AccessControl
        header('Access-Control-Allow-Origin: *'); 
        if (!parent::beforeAction($action)) {
            return false;
        }

        // other custom code here

        return true; // or false to not run the action
    }

    /**
    /**
     * Delivers the content based on the arguments
     * @param $type | String : email, pdf
     * @param 
     *
    public function actionDeliver($type = 'email')
    {
        // if (!Yii::$app->user->isGuest isset(Yii::$app->request->post()['notify'])) {
            
            // $notify = Yii::$app->request->post()['notify'];
            $notify = [
                'header'  => 
                'content' =>
                'footer'  => 
            ];

        // }
    }

    */

    // public function actionCreate()
    // {

    // }

    // public function actionView()
    // {
    //     return "SHOO";
    // }
}