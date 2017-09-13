<?php

namespace app\modules\v1\killbill\controllers;

use yii\rest\ActiveController;
use app\modules\v1\killbill\models\Killbill;

/**
 * Default controller for the `killbill` module
 */
class DefaultController extends ActiveController
{

    private $killbill;
    public $modelClass = false;

    public function init()
    {
        parent::init();

        $this->killbill = new Killbill([
            'serverUrl' => $this->module->serverUrl,
            'key' => $this->module->key,
            'secret' => $this->module->secret
        ]);
    }

    public function actions()
    {
        $actions = parent::actions();

        // disable all the default rest actions
        unset($actions['update'], $actions['delete'], $actions['view'], $actions['create'], $actions['index']);
        return $actions;
    }

    public function actionIndex()
    {
        return $this->killbill->getAccount();
    }

    public function actionCreateAccount()
    {
        Yii::$app->request->post();
        return $this->killbill->createAccount();
    }

    public function actionDeleteAccount()
    {
        return 'delete account';
    }

    public function actionSubscribe()
    {
        return 'create subscription';
    }

    public function actionUnsubscribe()
    {
        return 'delete subscription';
    }

    public function actionGetInvoice()
    {
        return 'retrieve invoice and convert to PDF';
    }

    public function actionPaymentdata()
    {
        return 'update payment information';
    }
}