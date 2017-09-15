<?php

namespace api\modules\v1\account;

/**
 * project module definition class
 */
class Account extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'api\modules\v1\account\controllers';
    public $modelNamespace = 'api\modules\v1\account\models';


    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        \Yii::setAlias('accountView', dirname(__FILE__) . '/views');

        // custom initialization code goes here
    }
}
