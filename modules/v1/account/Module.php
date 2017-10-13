<?php

namespace api\modules\v1\account;

use Yii;

/**
 * project module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'api\modules\v1\account\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        // Initialize requirements for the module
        $this->bootstrap();
    }

    private function bootstrap()
    {
        Yii::setAlias('accountView', dirname(__FILE__) . '/views');
        Yii::setAlias('migrationPath', dirname(__FILE__) . '/console/migrations');
    }
}