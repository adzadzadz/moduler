<?php

namespace api\modules\account\controllers;

use Yii;
use yii\web\Controller;
use yii\rest\ActiveController;

/**
 * Default controller for the `account` module
 */
class DefaultController extends Controller
{
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->render('index');
    }
}