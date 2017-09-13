<?php

namespace api\modules\v1\project\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\FncProject;
use common\models\FncConfig;
use frontend\models\Project;
use frontend\models\Fetcher;
use yii\helpers\ArrayHelper;

/**
 * Default controller for the `project` module
 */
class ProjectController extends ActiveController
{
    public $modelClass = 'common\models\FncProject';

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
            // 'verbs' => [
            //     'class' => VerbFilter::className(),
            //     'actions' => [
            //         'index' => ['GET', 'HEAD'],
            //         'view' => ['GET', 'HEAD'],
            //         'create' => ['POST'],
            //         'update' => ['PUT', 'PATCH'],
            //         'delete' => ['DELETE'],
            //     ],
            // ],
        ]);
    }

    public function actions()
    {
        $actions = parent::actions();

        // disable the "delete" and "create" actions
        unset($actions['create']);
        return $actions;
    }

    /**
     * Selects the project for the application
     */
    public function actionSwitchProject($id = null)
    {
        $config = FncConfig::selectProject($id);
        $project = FncProject::findOne($id);
        $data = [
            'id'   => $project->id,
            'name' => $project->name
        ];
        return $data;
    }

    /**
     * Creates a new project
     * Requires 'name' and 'group_id' (optional) via POST method
     */
    public function actionCreate()
    {
        $project = new Project;

        if ($project->load(Yii::$app->request->post(), '') && $result = $project->createProject()) {
            return Fetcher::formatProject($result);
        }
        return $project->getErrors();
    }

    /**
     * Fetches the project groups and its projects
     */
    // public function actionGetProjects()
    // {
    //     $projects = FncProject::find()->where(['status' => FncProject::STATUS_ACTIVE])->all();
    //     $data = [];
    //     foreach ($projects as $each) {
          
    //     }
    //     return !empty($data) ? $data : false;
    // }
}