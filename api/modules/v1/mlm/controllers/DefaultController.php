<?php

namespace api\modules\v1\mlm\controllers;

use yii\web\Controller;

/**
 * Default controller for the `mlm` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }
}