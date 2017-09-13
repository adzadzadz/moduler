<?php

namespace api\modules\v1\project\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\helpers\ArrayHelper;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\FncProjectAction;

/**
* Project Action Controller
*/
class ActionController extends ActiveController
{
    public $modelClass = 'common\models\FncProjectAction';

    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::className(),
                'denyCallback' => function ($rule, $action) {
                    return $this->redirect(['/guest/is-guest']);
                },
                'rules' => [
                    [
                        // 'actions' => ['logout', 'settings'],
                        'allow' => true,
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
        unset($actions['create']);
        return $actions;
    }

    public function actionCreate()
    {
        // return Yii::$app->strepzConfig;
        if (Yii::$app->strepzConfig->selectedProject == null) {
            return [
                'error' => 'No project selected',
                'option' => '/api/project/switch/<id>'
            ];
        }
        $model = new FncProjectAction;

        if ($model->load(Yii::$app->request->post(), '') && $result = $model->create()) {
            return $model;
        }
        return $model->getErrors();
    }
}