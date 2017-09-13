<?php
namespace api\modules\v1\account\models;

use Yii;
use yii\base\Model;
use api\modules\v1\account\models\FncUser;
use common\models\FncUserProject as UserProject;
use common\models\FncConfig;

/**
 * Data Formatter for json receiver
 */
class Fetcher extends Model
{
    /**
     * Fetches all required data for the strepz application
     * @param integer id
     * @return array
     */
    public static function getUser($id)
    {
        // Need to create new authManager object for better serving
        $authManager = Yii::$app->authManager;
        $authManager->ruleTable = '{{%' . Yii::$app->strepzConfig->company_id . '_auth_rule}}';
        $authManager->itemTable = '{{%' . Yii::$app->strepzConfig->company_id . '_auth_item}}';
        $authManager->itemChildTable = '{{%' . Yii::$app->strepzConfig->company_id . '_auth_item_child}}';
        $authManager->assignmentTable = '{{%' . Yii::$app->strepzConfig->company_id . '_auth_assignment}}';
        $authManager->db = Yii::$app->strepzDbManager->getFncDb();

        $user = FncUser::find()
            ->where(['id' => $id])
            ->joinWith('authAssignment')
            ->one();

        // Set Roles
        $auth = [];
        foreach ($user->authAssignment as $assigned) {
            $auth['roles'] = [
                $assigned->item_name => [
                    'name' => $assigned->item_name,
                    // 'children' => $authManager->getChildren($assigned->item_name),
                ]
            ];
        }

        $auth['permissions'] = $authManager->getPermissionsByUser(Yii::$app->user->id);

        // Find projects
        $projects = UserProject::find()
            ->where(['user_id' => $id])
            ->joinWith('project')
            ->all();

        // format projects if available
        if ($projects !== null) {
            $projectData = [];
            foreach ($projects as $userProject) {
                foreach ($userProject->project as $project) {
                    // return $project->name;
                    $projectData[] = [
                        'id'   => $project->id,
                        'name' => $project->name
                    ];
                }
            }
        }
        // format selected project
        $selectedProjectData = FncConfig::getSelectedProject();
        if ($selectedProjectData) {
            $project = [
                'id'   => $selectedProjectData->id,
                'name' => $selectedProjectData->name,
            ];
        } else {
            $project = false;
        }

        // Format all data as required
        $data = [
            'info' => [
                'id' => $user->id,
                'email' => $user->email,
                'firstname' => $user->firstname,
                'middlename' => $user->middlename,
                'lastname' => $user->lastname,
                // 'language' => $user->language,
                'status' => $user->status,
            ],
            'auth' => $auth,
            'projects' => $projectData,
            'project' => $project,
        ];

        return $data;
    }

    /**
     * Formats the project data
     * @param \common\models\FncProject project 
     * @return array
     */
    public static function formatProject($project)
    {
        $data = [
            'name' => $project->name
        ];

        return $data;
    }

    public static function filterUserData($user)
    {
        $data = [
            'id' => $user->id,
            'firstname' => $user->firstname,
            'middlename' => $user->middlename,
            'lastname' => $user->lastname,
            'email' => $user->email
        ];
        return $data;
    }

}