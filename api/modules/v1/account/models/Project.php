<?php
namespace frontend\models;

use Yii;
use yii\db\ActiveRecord;
use common\models\FncProject;
use common\models\FncProjectGroup as ProjectGroup;
use common\models\FncUserProject as UserProject;

class Project extends FncProject
{
    // Project Group
    public $description;

    public function rules()
    {
        return [
            [['name'], 'required'],

            // UserProject
            // [['user_id', 'project_id'], 'integer'],

            // Project
            ['name', 'string', 'max' => 255],
            ['project_group_id', 'integer'],
            ['project_group_id', 'default', 'value' => 1],

            // ProjectGroup
            [['name', 'description'], 'string', 'max' => 255],
            
            ['status', 'default', 'value' => FncProject::STATUS_ACTIVE],
            ['status', 'in', 'range' => [FncProject::STATUS_ACTIVE, FncProject::STATUS_UNVERIFIED, FncProject::STATUS_DELETED]],
        ];
    }

    public function createProject()
    {
        if ($this->validate()) {
            $project = new FncProject;
            $userProject = new UserProject;

            $project->name = $this->name;
            if ($project->save()) {
                $userProject->user_id = Yii::$app->user->id;
                $userProject->project_id = $project->id;

                if ($userProject->save()) {
                    return $project;
                }
            }
            return false;
        }
    }
}