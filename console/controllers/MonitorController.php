<?php 
namespace console\controllers;

use Yii;
use yii\console\Controller;
use backend\models\Environments;
use console\models\InstanceStatus;
use console\models\ScriptStatus;

class MonitorController extends Controller
{
	public $script_name;
	public $instance_name;
	public $status_code;
	public $type = 'fe';
	public $description = '';

	public function options($actionID)
    {
        return array_merge(
            parent::options($actionID),
            ['script_name', 'instance_name', 'status_code', 'type', 'description'] // global for all actions
        );
    }

	public function afterAction($action, $result)
	{
		$result = parent::afterAction($action, $result);
	    $this->stdout("\n\n");
	    return $result;
	}

	//$ sudo php /opt/strepz-be-appl/yii monitor/update-instance-status --instance_name="eu-vm-fe-01" --status_code="10" --type="fe" --description="sample description"
	public function actionUpdateInstanceStatus()
	{
		if ($result = InstanceStatus::saveStatus($this->instance_name, $this->status_code, $this->type, $this->description)) {
			$this->stdout("\n $this->instance_name status updated to $this->status_code.");
			return self::EXIT_CODE_NORMAL;
		}
		$this->stdout("\n $this->instance_name status update failed.");
		return self::EXIT_CODE_ERROR;
	}

	//$ sudo php /opt/strepz-be-appl/yii monitor/get-instance-status --instance_name="eu-vm-fe-01" --type="fe"
	public function actionGetInstanceStatus()
	{
		if (!is_null($result = InstanceStatus::getStatus($this->instance_name, $this->type))) {
			$this->stdout("\n Instance_name: $result->instance_name");
			$this->stdout("\n Type: $result->type");
			$this->stdout("\n Description: $result->description");
			$this->stdout("\n Status: $result->status");

			$time_diff = strtotime("now") - $result->updated_at;
			// date("Y-m-d - h:m:s", $result->updated_at)
			$this->stdout("\n Last update: " . $this->secondsToTime($time_diff) . " ago.");

			return self::EXIT_CODE_NORMAL;
		}
		$this->stdout("\n $this->instance_name status fetch failed");
		return self::EXIT_CODE_ERROR;
	}

	//$ sudo php /opt/strepz-be-appl/yii monitor/update-script-status --script_name="" --status_code="10" --description="sample description"
	public function actionUpdateScriptStatus()
	{
		if ($result = \backend\modules\yii2monitoring\models\ScriptStatus::saveStatus($this->script_name, $this->status_code, $this->description)) {
			$this->stdout("\n $this->script_name status updated to $this->status_code.");
			return self::EXIT_CODE_NORMAL;
		}
		$this->stdout("\n $this->script_name status update failed.");
		return self::EXIT_CODE_ERROR;
	}

	//$ sudo php /opt/strepz-be-appl/yii monitor/get-script-status --script_name=""
	public function actionGetScriptStatus()
	{
		if (!is_null($result = ScriptStatus::getStatus($this->script_name))) {
			$this->stdout("\n Script name: $result->script_name");
			$this->stdout("\n Description: $result->description");
			$this->stdout("\n Status: $result->status");

			$time_diff = strtotime("now") - $result->updated_at;
			// date("Y-m-d - h:m:s", $result->updated_at)
			$this->stdout("\n Last update: " . $this->secondsToTime($time_diff) . " ago.");

			return self::EXIT_CODE_NORMAL;
		}
		$this->stdout("\n $this->script_name status fetch failed");
		return self::EXIT_CODE_ERROR;
	}

	public function secondsToTime($seconds) {
	    $dtF = new \DateTime("@0");
	    $dtT = new \DateTime("@$seconds");
	    return $dtF->diff($dtT)->format('%a days, %h hours, %i minutes and %s seconds');
	}
}