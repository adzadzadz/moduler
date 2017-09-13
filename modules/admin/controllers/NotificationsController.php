<?php

namespace api\modules\admin\controllers;

use Yii;
use yii\web\Controller;
use api\modules\admin\models\Notifications;

/**
 * Default controller for the `admin` module
 */
class NotificationsController extends Controller
{
    public $layout = 'main';

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionCreate()
    {
        $model = new Notifications;
        // return var_dump(Yii::$app->request->post());
        $model->load(Yii::$app->request->post());
        // return var_dump($model);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }
        return $this->redirect(['index']);
    }
}