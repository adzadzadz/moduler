<?php 

namespace common\components\rbac;

use Yii;

/**
* Strepz RBAC DbManager
*/
class DbManager extends \yii\rbac\DbManager
{
    public function assign($role, $userId)
    {
        $assignment = new \yii\rbac\Assignment([
            'userId' => $userId,
            'roleName' => $role->name,
            'createdAt' => time(),
        ]);

        $this->db->createCommand()
            ->insert($this->assignmentTable, [
                'user_id' => $assignment->userId,
                'project_id' => Yii::$app->strepzConfig->selectedProject,
                'item_name' => $assignment->roleName,
                'created_at' => $assignment->createdAt,
            ])->execute();

        return $assignment;
    }
}