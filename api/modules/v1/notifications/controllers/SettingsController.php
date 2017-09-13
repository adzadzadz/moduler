<?php

namespace api\modules\v1\notifications\controllers;

use Yii;
use yii\web\Controller;
use api\modules\v1\notifications\models\Template;

/**
 * Default controller for the `main` module
 */
class SettingsController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        $templates = Template::findAll(['status' => 10]);
        return $this->render('index', [
            'result' => '',
            'templates' => $templates
        ]);
    }

    public function actionCreate()
    {
        $model = new Template;

        // var_dump(Yii::$app->request->post());
        // return var_dump($model->load(Yii::$app->request->post()));

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            // Nothing to do 
        }
        $result = $model->getErrors();
        return $this->render('index', [
            'result' => $result
        ]);
    }
}