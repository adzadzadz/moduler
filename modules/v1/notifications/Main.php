<?php

namespace api\modules\v1\notifications;

/**
 * main module definition class
 */
class Main extends \yii\base\Module
{
    public $db;
    
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'api\modules\v1\notifications\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }
}