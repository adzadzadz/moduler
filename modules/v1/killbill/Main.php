<?php

namespace app\modules\v1\killbill;

/**
 * killbill module definition class
 */
class Main extends \yii\base\Module
{
    public $serverUrl;
    public $key;
    public $secret;
    public $username;
    public $password;

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\modules\v1\killbill\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }
}
