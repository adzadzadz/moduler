<?php

namespace api\modules\v1\account\models\rbac;

use Yii;
use yii\base\Model;
use api\modules\v1\account\models\rbac\AuthItem;
use api\modules\v1\account\models\rbac\AuthAssignment;

class Rbac extends Model
{
    /**
     * Initializes all roles required by the system.
     * @param yii\rbac\Role $auth
     * @return yii\rbac\Role | superuser
     */
	public static function initRoles($auth)
	{  
        $auth->db = Yii::$app->strepzDbManager->getFncDb();
        
        // Project Module
        // $projectNoAccess = $auth->createPermission('projectNoAccess');
        // $projectRead = $auth->createPermission('projectRead');
        // $projectAdd  = $auth->createPermission('projectAdd');
        // $projectFull = $auth->createPermission('projectFull');

        // Permissions
        // $noAccess = $auth->createPermission('noAccess');
        // $read = $auth->createPermission('read');
        // $add  = $auth->createPermission('add');
        // $full = $auth->createPermission('full');

        // Permissions
        // $noAccess = $auth->createPermission('noAccess');
        // $read = $auth->createPermission('read');
        // $add  = $auth->createPermission('add');
        // $full = $auth->createPermission('full');

        // $auth->add($noAccess);
        // $auth->add($read);
        // $auth->add($add);
        // $auth->add($full);

        // Primary Roles
        $superadmin = $auth->createRole('superadmin');
        $superadmin->description = 'Daddy of all kids.';
        $auth->add($superadmin);

        $admin = $auth->createRole('admin');
        $admin->description = 'Full administration control.';
        $auth->add($admin);

        $projectmanager = $auth->createRole('projectManager');
        $projectmanager->description = 'Huge power over the projects under this guy.';
        $auth->add($projectmanager);

        $audit = $auth->createRole('audit');
        $audit->description = 'Have read permission over the Project pages.';
        $auth->add($audit);

        $programmanager = $auth->createRole('programManager');
        $programmanager->description = 'The program manager guy who is the doer of his doings.';
        $auth->add($programmanager);

        $sme = $auth->createRole('sme');
        $sme->description = 'The guy who does all the things he does best.';
        $auth->add($sme);

        $auth->addChild($superadmin, $admin);
        $auth->addChild($superadmin, $programmanager);
        $auth->addChild($admin, $projectmanager);
        $auth->addChild($projectmanager, $audit);
        $auth->addChild($projectmanager, $sme);

        $rbac = new Rbac;
        if (!$rbac->setProjectPermissions()) return false;
        if (!$rbac->setPorfolioPermissions()) return false;
        if (!$rbac->setSettingsPermissions()) return false;

        return $superadmin;
	}

    /**
     * Initializes all Project permissions required by the system.
     *
     * @return 
     */
    public function setProjectPermissions()
    {
        // - Project (superadmin, Project Mangager) (Audit : Read)
        //   * Reporting 
        //   * Actions (SME: Read, Add)
        //   * Risk (SME: Read)
        //   * Decision
        //   * Issue
        //   * Configuration

        $auth = \Yii::$app->authManager;

        // Top Level Permissions
        $projectNoAccess = $auth->createPermission('projectNoAccess');
        $projectRead = $auth->createPermission('projectRead');
        $projectAdd  = $auth->createPermission('projectAdd');
        $projectFull = $auth->createPermission('projectFull');

        $auth->add($projectNoAccess);
        $auth->add($projectRead);
        $auth->add($projectAdd);
        $auth->add($projectFull);

        // Reporting Permissions
        $projectReportingNoAccess = $auth->createPermission('projectReportingNoAccess');
        $projectReportingRead = $auth->createPermission('projectReportingRead');
        $projectReportingAdd  = $auth->createPermission('projectReportingAdd');
        $projectReportingFull = $auth->createPermission('projectReportingFull');

        $auth->add($projectReportingNoAccess);
        $auth->add($projectReportingRead);
        $auth->add($projectReportingAdd);
        $auth->add($projectReportingFull);
        $auth->addChild($projectReportingFull, $projectReportingAdd);
        $auth->addChild($projectReportingAdd, $projectReportingRead);

        // Actions Permissions
        $projectActionsNoAccess = $auth->createPermission('projectActionsNoAccess');
        $projectActionsRead = $auth->createPermission('projectActionsRead');
        $projectActionsAdd  = $auth->createPermission('projectActionsAdd');
        $projectActionsFull = $auth->createPermission('projectActionsFull');

        $auth->add($projectActionsNoAccess);
        $auth->add($projectActionsRead);
        $auth->add($projectActionsAdd);
        $auth->add($projectActionsFull);
        $auth->addChild($projectActionsFull, $projectActionsAdd);
        $auth->addChild($projectActionsAdd, $projectActionsRead);

        // Risk Permissions
        $projectRiskNoAccess = $auth->createPermission('projectRiskNoAccess');
        $projectRiskRead = $auth->createPermission('projectRiskRead');
        $projectRiskAdd  = $auth->createPermission('projectRiskAdd');
        $projectRiskFull = $auth->createPermission('projectRiskFull');

        $auth->add($projectRiskNoAccess);
        $auth->add($projectRiskRead);
        $auth->add($projectRiskAdd);
        $auth->add($projectRiskFull);
        $auth->addChild($projectRiskFull, $projectRiskAdd);
        $auth->addChild($projectRiskAdd, $projectRiskRead);

        // Decision Permissions
        $projectDecisionNoAccess = $auth->createPermission('projectDecisionNoAccess');
        $projectDecisionRead = $auth->createPermission('projectDecisionRead');
        $projectDecisionAdd  = $auth->createPermission('projectDecisionAdd');
        $projectDecisionFull = $auth->createPermission('projectDecisionFull');

        $auth->add($projectDecisionNoAccess);
        $auth->add($projectDecisionRead);
        $auth->add($projectDecisionAdd);
        $auth->add($projectDecisionFull);
        $auth->addChild($projectDecisionFull, $projectDecisionAdd);
        $auth->addChild($projectDecisionAdd, $projectDecisionRead);

        // Issue Permissions
        $projectIssueNoAccess = $auth->createPermission('projectIssueNoAccess');
        $projectIssueRead = $auth->createPermission('projectIssueRead');
        $projectIssueAdd  = $auth->createPermission('projectIssueAdd');
        $projectIssueFull = $auth->createPermission('projectIssueFull');

        $auth->add($projectIssueNoAccess);
        $auth->add($projectIssueRead);
        $auth->add($projectIssueAdd);
        $auth->add($projectIssueFull);
        $auth->addChild($projectIssueFull, $projectIssueAdd);
        $auth->addChild($projectIssueAdd, $projectIssueRead);

        // Configuration Permissions
        $projectConfigurationNoAccess = $auth->createPermission('projectConfigurationNoAccess');
        $projectConfigurationRead = $auth->createPermission('projectConfigurationRead');
        $projectConfigurationAdd  = $auth->createPermission('projectConfigurationAdd');
        $projectConfigurationFull = $auth->createPermission('projectConfigurationFull');

        $auth->add($projectConfigurationNoAccess);
        $auth->add($projectConfigurationRead);
        $auth->add($projectConfigurationAdd);
        $auth->add($projectConfigurationFull);
        $auth->addChild($projectConfigurationFull, $projectConfigurationAdd);
        $auth->addChild($projectConfigurationAdd, $projectConfigurationRead);

        // Set project permission tree
        // Read
        $auth->addChild($projectRead, $projectReportingRead);
        $auth->addChild($projectRead, $projectActionsRead);
        $auth->addChild($projectRead, $projectRiskRead);
        $auth->addChild($projectRead, $projectDecisionRead);
        $auth->addChild($projectRead, $projectIssueRead);
        $auth->addChild($projectRead, $projectConfigurationRead);

        // Add
        $auth->addChild($projectAdd, $projectRead);
        $auth->addChild($projectAdd, $projectReportingAdd);
        $auth->addChild($projectAdd, $projectActionsAdd);
        $auth->addChild($projectAdd, $projectRiskAdd);
        $auth->addChild($projectAdd, $projectDecisionAdd);
        $auth->addChild($projectAdd, $projectIssueAdd);
        $auth->addChild($projectAdd, $projectConfigurationAdd);

        // Full
        $auth->addChild($projectFull, $projectAdd);
        $auth->addChild($projectFull, $projectReportingFull);
        $auth->addChild($projectFull, $projectActionsFull);
        $auth->addChild($projectFull, $projectRiskFull);
        $auth->addChild($projectFull, $projectDecisionFull);
        $auth->addChild($projectFull, $projectIssueFull);
        $auth->addChild($projectFull, $projectConfigurationFull);

        // Set permissions for certain roles
        // Project Manager has Full control over project module
        $projectManager = $auth->getRole('projectManager');
        $auth->addChild($projectManager, $projectFull);

        // Audit has read access over project module
        $audit = $auth->getRole('audit');
        $auth->addChild($audit, $projectRead);

        // SME has read access for Access and Risk pages under project module
        $sme = $auth->getRole('sme');
        $auth->addChild($sme, $projectActionsAdd);
        $auth->addChild($sme, $projectRiskRead);

        return true;
    }

    /**
     * Initializes all Portfolio permissions required by the system.
     *
     * @return 
     */
    public function setPorfolioPermissions()
    {
        // - Portfolio (Program Manager)
        //   * Reporting (Project Manager: Read) #current setup has full permissions
        //   * Configuration 

        $auth = \Yii::$app->authManager;

        // Top Level Permissions
        $portfolioNoAccess = $auth->createPermission('portfolioNoAccess');
        $portfolioRead = $auth->createPermission('portfolioRead');
        $portfolioAdd  = $auth->createPermission('portfolioAdd');
        $portfolioFull = $auth->createPermission('portfolioFull');

        $auth->add($portfolioNoAccess);
        $auth->add($portfolioRead);
        $auth->add($portfolioAdd);
        $auth->add($portfolioFull);

        // Reporting Page
        $portfolioReportingNoAccess = $auth->createPermission('portfolioReportingNoAccess');
        $portfolioReportingRead = $auth->createPermission('portfolioReportingRead');
        $portfolioReportingAdd  = $auth->createPermission('portfolioReportingAdd');
        $portfolioReportingFull = $auth->createPermission('portfolioReportingFull');

        $auth->add($portfolioReportingNoAccess);
        $auth->add($portfolioReportingRead);
        $auth->add($portfolioReportingAdd);
        $auth->add($portfolioReportingFull);
        $auth->addChild($portfolioReportingFull, $portfolioReportingAdd);
        $auth->addChild($portfolioReportingAdd, $portfolioReportingRead);

        // Configuration Page
        $portfolioConfigurationNoAccess = $auth->createPermission('portfolioConfigurationNoAccess');
        $portfolioConfigurationRead = $auth->createPermission('portfolioConfigurationRead');
        $portfolioConfigurationAdd  = $auth->createPermission('portfolioConfigurationAdd');
        $portfolioConfigurationFull = $auth->createPermission('portfolioConfigurationFull');

        $auth->add($portfolioConfigurationNoAccess);
        $auth->add($portfolioConfigurationRead);
        $auth->add($portfolioConfigurationAdd);
        $auth->add($portfolioConfigurationFull);
        $auth->addChild($portfolioConfigurationFull, $portfolioConfigurationAdd);
        $auth->addChild($portfolioConfigurationAdd, $portfolioConfigurationRead);

        // Permission Tree
        $auth->addChild($portfolioRead, $portfolioReportingRead);
        $auth->addChild($portfolioRead, $portfolioConfigurationRead);

        $auth->addChild($portfolioAdd, $portfolioRead);
        $auth->addChild($portfolioAdd, $portfolioReportingAdd);
        $auth->addChild($portfolioAdd, $portfolioConfigurationAdd);

        $auth->addChild($portfolioFull, $portfolioAdd);
        $auth->addChild($portfolioFull, $portfolioReportingFull);
        $auth->addChild($portfolioFull, $portfolioConfigurationFull);

        $programManager = $auth->getRole('programManager');
        $auth->addChild($programManager, $portfolioFull);

        return true;
    }

    /**
     * Initializes all Settings permissions required by the system.
     *
     * @return 
     */
    public function setSettingsPermissions()
    {
        // - Settings (superadmin)
        //   * Finance
        //   * User Management

        $auth = \Yii::$app->authManager;

        // Top Level Permissions
        $settingsNoAccess = $auth->createPermission('settingsNoAccess');
        $settingsRead = $auth->createPermission('settingsRead');
        $settingsAdd  = $auth->createPermission('settingsAdd');
        $settingsFull = $auth->createPermission('settingsFull');

        $auth->add($settingsNoAccess);
        $auth->add($settingsRead);
        $auth->add($settingsAdd);
        $auth->add($settingsFull);

        // Finance page
        $settingsFinanceNoAccess = $auth->createPermission('settingsFinanceNoAccess');
        $settingsFinanceRead = $auth->createPermission('settingsFinanceRead');
        $settingsFinanceAdd  = $auth->createPermission('settingsFinanceAdd');
        $settingsFinanceFull = $auth->createPermission('settingsFinanceFull');

        $auth->add($settingsFinanceNoAccess);
        $auth->add($settingsFinanceRead);
        $auth->add($settingsFinanceAdd);
        $auth->add($settingsFinanceFull);
        $auth->addChild($settingsFinanceFull, $settingsFinanceAdd);
        $auth->addChild($settingsFinanceAdd, $settingsFinanceRead);

        // User Management page
        $settingsUserManagementNoAccess = $auth->createPermission('settingsUserManagementNoAccess');
        $settingsUserManagementRead = $auth->createPermission('settingsUserManagementRead');
        $settingsUserManagementAdd  = $auth->createPermission('settingsUserManagementAdd');
        $settingsUserManagementFull = $auth->createPermission('settingsUserManagementFull');

        $auth->add($settingsUserManagementNoAccess);
        $auth->add($settingsUserManagementRead);
        $auth->add($settingsUserManagementAdd);
        $auth->add($settingsUserManagementFull);
        $auth->addChild($settingsUserManagementFull, $settingsUserManagementAdd);
        $auth->addChild($settingsUserManagementAdd, $settingsUserManagementRead);

        // Permission tree
        $auth->addChild($settingsRead, $settingsFinanceRead);  
        $auth->addChild($settingsRead, $settingsUserManagementRead);

        $auth->addChild($settingsAdd, $settingsRead);
        $auth->addChild($settingsAdd, $settingsFinanceAdd);
        $auth->addChild($settingsAdd, $settingsUserManagementAdd);

        $auth->addChild($settingsFull, $settingsAdd);
        $auth->addChild($settingsFull, $settingsFinanceFull);
        $auth->addChild($settingsFull, $settingsUserManagementFull);

        $superadmin = $auth->getRole('superadmin');
        $auth->addChild($superadmin, $settingsFull);

        return true;

    }
}