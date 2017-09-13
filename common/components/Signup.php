<?php 
namespace common\components;

use Yii;
use yii\base\Object;

class Signup extends Object
{
	/*
	 * @property|array $data_locations
	 * Check frontend/modules/frontendmanager/models/ConfigFiles.php for Data_location namming.
	 * Will setup class for these config later on.
	 *
	 **/
	public $data_locations = [];

	public function init()
	{
		parent::init();

		$data_location_result = $this->getDataLocations();
	}

	public function getDataLocations()
	{
		$signup_config = require (Yii::getAlias('@common/config/strepz/signup.php'));
		$this->data_locations = $signup_config['data_locations'];
		return $this->data_locations;
	}

}