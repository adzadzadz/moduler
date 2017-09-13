<?php

namespace console\modules\frontendcontroller\controllers;

use yii\console\Controller;

class DefaultController extends Controller
{
    public function actionIndex()
    {
        $this->stdout('GET OUT! YOU ARE EVIL!');
    }
}
