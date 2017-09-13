<?php 
namespace console\controllers;

use Yii;
use yii\console\Controller;
use yii\console\controllers\MigrateController;

/**
 * Usage
 * php yii migrations/migrate-backend
 */
class MigrationsController extends MigrateController
{
	public $db;
	public $glb_sys_db = 'glb_sys_db_01';
	public $glb_cnf_db = 'glb_cnf_db_01';
    public $glb_ntf_db = 'glb_ntf_db_01';
	public $migrationPath = '@frontend/migrations';
	public $companyId;

	/**
	 * Modified method to work with strepz structure, "db" property is not necessary when working with this class
     * This method is invoked right before an action is to be executed (after all possible filters.)
     * It checks the existence of the [[migrationPath]].
     * @param \yii\base\Action $action the action to be executed.
     * @return boolean whether the action should continue to be executed.
     */
    public function beforeAction($action)
    {
        return true;
    }

    /**
     * Common migration method
     */
    public function actionMigrateStrepz()
    {
    	Yii::$app->runAction('migrate', [
			'migrationPath' => $this->migrationPath, 
			'db' => $this->db,
			'interactive' => false
		]);
    	return true;
    }

    /**
     * Migrate Global User Tables
     */
	public function actionMigrateFrontendGlb()
	{
		$this->runAction('migrate-strepz', [
			'migrationPath' => '@console/migrations/migrations_glb_sys', 
			'db' => $this->glb_sys_db,
		]);
		$this->stdout("Frontend tables succesfully migrated");
	}

	/**
     * Migrate Global Config Tables
     */
    public function actionMigrateFrontendCnf()
    {
        // Reminder: Create migration trigger for glb, fnc, dbs
        $this->runAction('migrate-strepz', [
            'migrationPath' => '@console/migrations/migrations_glb_cnf', 
            'db' => $this->glb_cnf_db, 
        ]);
        $this->stdout("Frontend tables succesfully migrated");
    }

    /**
     * Migrate Notification Tables
     */
    public function actionMigrateNotifications()
    {
        // Reminder: Create migration trigger for glb, fnc, dbs
        $this->runAction('migrate-strepz', [
            'migrationPath' => '@console/migrations/migrations_notifications', 
            'db' => $this->glb_ntf_db, 
        ]);
        $this->stdout("Notifications tables succesfully migrated");
    }
}