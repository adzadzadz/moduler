<?php

namespace console\modules\frontendcontroller\controllers;

use Yii;
use yii\console\Controller;
use console\modules\frontendcontroller\models\ConfigBuilder;
use console\models\middleware\EnvironmentData;
use console\models\middleware\Connections;
use console\models\middleware\Dbs;

class ConfigController extends Controller
{
    public $environment_id;
    public $dap_status;
    public $domain = 'strepz.com';

    public function options($actionID)
    {
        return array_merge(
            parent::options($actionID), [
                'environment_id',
                'dap_status',
                'domain'
            ]
        );
    }

    public function actionIndex()
    {
        $this->stdout('GET OUT! YOU ARE EVIL!');
    }

    /**
     * sudo php yii frontend/config/init --environment_id=$HOSTNAME --dap_status=$DAP_STATUS
     * sudo php yii frontend/config/init --environment_id= --dap_status=dev
     */ 
    public function actionInit()
    {
        $this->stdout("\nStrepz Init options: --environment_id=$this->environment_id --dap_status=$this->dap_status \n");
        $this->stdout("\nStrepz Init: START\n");
        $environments = [
            'local' => 'local',
            'dev' => 'development',
            'acc' => 'acceptance',
            'prod' => 'production'
        ];
    	// Insert everything that is needed here.
        // Choose the right config files using php init
        exec(BEFORE_CMD . "php " . Yii::getAlias("@root_path") . "/init --env=" . $environments[$this->dap_status] . " --overwrite=All &");
        exec(BEFORE_CMD . "php " . Yii::getAlias("@root_path") . "/yii frontend/config/build --environment_id=$this->environment_id &");
        $this->stdout("\nStrepz Init: END\n");
    }
    
    public function actionBuild()
    {
        $this->stdout("\nStrepz Config Setup: START\n");
        $this->stdout("Setting up config for Intance name: $this->environment_id");
        
        $envData = EnvironmentData::getData($this->environment_id);
        $connections = Connections::findAll(['environment_id' => $this->environment_id]);
        $dbs = Dbs::findAll(['environment_id' => $this->environment_id]);
        $configBuilder = new ConfigBuilder;
        $dbNaming = require(dirname(__DIR__) . "/config/main.php");

        $contentMain = $this->renderFile("@console/modules/frontendcontroller/views/config/main.php", [ 
            'environment_id' => $this->environment_id,
            'connections' => $connections,
            'dbs' => $dbs,
            'dbNaming' => $dbNaming['dbNaming'],
        ]);

        if ($configBuilder->write('main.php', $contentMain)) {
            $this->stdout("common/config/strepz/main.php has been succesfully created\n\n");
        }

        $contentParams = $this->renderFile("@console/modules/frontendcontroller/views/config/params.php", [ 
            'environment_id' => $this->environment_id,
            'envData' => $envData,
            'domain' => $this->domain,
        ]);

        if ($configBuilder->write('params.php', $contentParams)) {
            $this->stdout("common/config/strepz/params.php has been succesfully created\n\n");
        }

        // Set strepz-fe-frnt config file for proper api contact.
        $contentFrntConfig = $this->renderFile("@console/modules/frontendcontroller/views/frnt/apiconfig.php", [
            'config_id' => $envData['config_id'],
            'build_no'  => $envData['build_no'],
            'region'    => $envData['region']
        ]);

        if ($configBuilder->write('api-config.js', $contentFrntConfig, Yii::getAlias("@root_parent") . '/strepz-fe-frnt')) {
            $this->stdout(Yii::getAlias("@root_parent") . "strepz-fe-frnt/api-config.js has been succesfully created\n\n");
        }

        $this->miwCleanup($this->environment_id);

        exec(BEFORE_CMD . "php " . Yii::getAlias("@root_path") . "/yii migrations/migrate-frontend-glb &");
        exec(BEFORE_CMD . "php " . Yii::getAlias("@root_path") . "/yii migrations/migrate-frontend-cnf &");
        // exec(BEFORE_CMD . "php " . Yii::getAlias("@root_path") . "/yii migrations/migrate-frontend-fnc &");

        $this->stdout("\nStrepz Config Setup: END\n");
        $this->stdout("\n\nEverything has been initiated succesfully"); #temporary end of function
    }

    private function miwCleanup($environment_id)
    {
        $this->stdout("Removing $environment_id's data");
        $result = EnvironmentData::deleteAll(['environment_id' => $environment_id]);
        $result .= Connections::deleteAll(['environment_id' => $environment_id]);
        $result .= \console\models\middleware\Dbs::deleteAll(['environment_id' => $environment_id]);
        $this->stdout("****DONE****");
    }

}