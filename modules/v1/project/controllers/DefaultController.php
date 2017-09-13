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

/**
 * Default controller for the `project` module
 */
class DefaultController extends ActiveController
{
    public $modelClass = 'common\models\FncProject';

    // public function behaviors()
    // {
    //     return [
    //         'access' => [
    //             'class' => AccessControl::className(),
    //             'denyCallback' => function ($rule, $action) {
    //                 return $this->redirect(['/guest/is-guest']);
    //             },
    //             'rules' => [
    //                 [
    //                     // 'actions' => ['logout', 'settings'],
    //                     'allow' => true,
    //                     'roles' => ['@'],
    //                 ],
    //             ],
    //         ],
    //         'verbs' => [
    //             'class' => VerbFilter::className(),
    //             'actions' => [
    //                 // 'logout' => ['post'],
    //             ],
    //         ],
    //     ];
    // }

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
        return json_encode($data);
    }

    /**
     * Creates a new project
     * Requires 'name' and 'group_id' (optional) via POST method
     */
    public function actionCreateProject()
    {
        $project = new Project;

        if ($project->load(Yii::$app->request->post()) && $project = $project->createProject()) {
            return json_encode(Fetcher::formatProject($project));
        }
        return json_encode(false);
    }

    /**
     * Fetches the project groups and its projects
     */
    public function actionGetProjects()
    {
        $projects = FncProject::find()->where(['status' => FncProject::STATUS_ACTIVE])->all();
        $data = [];
        foreach ($projects as $each) {
          
        }
        return !empty($data) ? json_encode($data) : json_encode(false);
    }
}
