<?php 
namespace common\components;

use Yii;
use yii\base\Object;
use common\models\replication\GlbUserCompany;
use common\models\replication\GlbUser;

class DbDataReplicator extends Object
{
	public $glbUser_dbs = [];
	public $current_glbUserDb;
	public $lang_dbs = [];

	private function getDbObject($type, $id, $data = [])
	{
		$locator = new \yii\di\ServiceLocator;
        $locator->set($id, $data);
        if ($type === 'user') {
        	$this->current_glbUserDb = $locator->get($id);
        }
        return $locator->get($id);
	}

	public function replicateGlobalData($data = [], $both = true)
	{
		// Replication is not needed when there is no DB set.
		if (empty($this->glbUser_dbs)) {
			return true;
		}

		$result_user = false;
		$result_userCompany = false;

		if (isset($data['user'])) {
			$result_user = $this->replicateGlobalUser($data['user']);
		}
		if (isset($data['userCompany'])) {
			$result_userCompany = $this->replicateGlobalUserCompany($data['userCompany']);
			return $result_userCompany;
		}
		if ($both) {
			if ($result_user && $result_userCompany) {
				return true;
			}
		} else {
			if ($result_user || $result_userCompany) {
				return true;
			}
		}		
		return false;
	}

	public function getGlbUserCompany($company_id)
	{
		return GlbUserCompany::findOne(['company_id' => $company_id]);
	}

	// MERGE ON REFACTOR 1.0
	private function replicateGlobalUser($data = [])
	{
		$result = false;
		foreach ($this->glbUser_dbs as $db_id => $db_data) {
			if ($this->getDbObject('user', $db_id, $db_data)) {
				$glbUser = GlbUser::findOne(['company_id' => $data['company_id']]);
				if ($glbUser === null) {
					$glbUser = new GlbUser;
				}
				foreach ($data as $key => $value) {
					$glbUser->$key = $value;	
				}
				if ($glbUser->save()) {
					$result = true;
				}
			}
		}
		return $result;
	}

	// MERGE ON REFACTOR 1.1
	private function replicateGlobalUserCompany($data = [])
	{
		$result = false;
		foreach ($this->glbUser_dbs as $db_id => $db_data) {
			if ($this->getDbObject('user', $db_id, $db_data)) {
				$glbUserCompany = GlbUserCompany::findOne(['company_id' => $data['company_id']]);
				if ($glbUserCompany === null) {
					$glbUserCompany = new GlbUserCompany;
				}
				foreach ($data as $key => $value) {
					$glbUserCompany->$key = $value;	
				}
				if ($glbUserCompany->save()) {
					$result = true;
				}
			}
		}
		return $result;
	}

	/*
	 * Language replication area
	 *
	 * Process: Get table names > get data for all tables
	 * > create tables if not set > insert all data
	 * This process should only be done on the backend
	 *
	 **/
	public function replicateTranslations()
	{
		// Need finalize db structure

		foreach ($lang_dbs as $db_id => $db_data) {
			if ($this->getDbObject('language', $db_id, $db_data)) {

			}
		}
	}

}