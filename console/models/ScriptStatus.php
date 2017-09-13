<?php

namespace console\models;

use Yii;
use yii\behaviors\TimestampBehavior;


class ScriptStatus extends \yii\db\ActiveRecord
{

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%script_status}}';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('mon_db_01');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['script_name', 'status'], 'required'],
            [['status', 'created_at', 'updated_at'], 'integer'],
            [['description', 'script_name'], 'string', 'max' => 255]
        ];
    }

    // \backend\modules\yii2monitoring\models\ScriptStatus::saveStatus($script_name, $status);
    public static function saveStatus($script_name, $status, $description = '')
    {
        // Check if environment already exist in monitoring db
        if (Static::isExist($script_name)) {
            // Update if env exist
            if ($update = Static::updateStatus($script_name, $status, $description)) {
                return $update;
            }
            return false;
        }
        // Build data
        $stat = new ScriptStatus;
        $stat->description = $description;
        $stat->script_name = $script_name;
        $stat->status = $status;
        // Save
        if ($stat->save()) {
            return $stat;
        }
        // Fail
        return false;
    }

    public static function updateStatus($script_name, $status, $description = '')
    {
        $env = Static::findOne(['script_name' => $script_name]);
        $env->description = $description;
        $env->script_name = $script_name;
        $env->status = $status;
        if ($env->save()) {
            return $env;
        }
        return false;
    }

    public static function isExist($script_name)
    {
        if (Static::findOne(['script_name' => $script_name]) !== null) {
            return true;
        }
        return false;
    }

    public static function getStatus($script_name)
    {
        return ScriptStatus::findOne(['script_name' => $script_name]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'description' => 'Description',
            'script_name' => 'Script Name',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}