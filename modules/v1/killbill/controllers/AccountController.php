<?php

namespace app\modules\v1\killbill\controllers;

use Yii;
use yii\rest\ActiveController;
use app\modules\v1\killbill\models\Account;

/**
 * Default controller for the `killbill` module
 */
class AccountController extends ActiveController
{
    private $account;
    public $modelClass = false;

    public function init()
    {
        parent::init();

        $this->account = new Account([
            'serverUrl' => $this->module->serverUrl,
            'key' => $this->module->key,
            'secret' => $this->module->secret
        ]);
    }

    public function actions()
    {
        $actions = parent::actions();

        // disable all the default rest actions
        unset($actions['options'], $actions['update'], $actions['delete'], $actions['view'], $actions['create'], $actions['index']);
        return $actions;
    }

    public function actionOptions()
    {
        return [
            'Options' => [
                [
                    'PATH' => '/api/v1/account',
                    'METHOD' => 'GET',
                    'INFO' => 'API usage'
                ],
                [
                    'PATH' => '/api/v1/account',
                    'METHOD' => 'POST',
                    'INFO' => 'Creates an account'
                ],
                [
                    'PATH' => '/api/v1/account',
                    'METHOD' => 'PATCH',
                    'INFO' => 'Updates an account'
                ],
                [
                    'PATH' => '/api/v1/account/<externalkey>',
                    'METHOD' => 'GET',
                    'INFO' => 'Gets user by externalKey'
                ],
                [
                    'PATH' => '/api/v1/account/<externalkey>',
                    'METHOD' => 'VIEW',
                    'INFO' => 'Gets user by externalKey'
                ],
                [
                    'PATH' => '/api/v1/account/<id>',
                    'METHOD' => 'DELETE',
                    'INFO' => 'Removes an account'
                ],
                [
                    'PATH' => '/api/v1/account',
                    'METHOD' => 'OPTIONS',
                    'INFO' => 'API usage'
                ] ,
            ]
        ];
    }

    public function actionView($externalkey)
    {   
        $this->account->externalKey = $externalkey;
        return $this->account->get();   
    }

    public function actionCreate()
    {   
        if ($this->account->load(['Account' => Yii::$app->request->post()]) && 
            $this->account->create('devAdz', 'Yii2 Testing')) {
            return true;
        }
        return false;
    }

    public function actionDeleteAccount()
    {
        return 'delete account';
    }
}