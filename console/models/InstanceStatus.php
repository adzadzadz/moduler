<?php

namespace console\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%frontend_status}}".
 *
 * @property integer $id
 * @property string $description
 * @property string $instance_name
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 */
class InstanceStatus extends \yii\db\ActiveRecord
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
        return '{{%instance_status}}';
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
            [['instance_name', 'status'], 'required'],
            [['status', 'created_at', 'updated_at'], 'integer'],
            [['description', 'instance_name', 'type'], 'string', 'max' => 255],

            ['type', 'default', 'value' => '']
        ];
    }

    // \backend\modules\yii2monitoring\models\InstanceStatus::saveStatus($instance_name, $status);
    public static function saveStatus($instance_name, $status, $type, $description)
    {
        // Check if environment already exist in monitoring db
        if (Static::isExist($instance_name, $type)) {
            // Update if env exist
            if ($update = Static::updateStatus($instance_name, $status, $type, $description)) {
                return $update;
            }
            return false;
        }
        // Build data
        $stat = new InstanceStatus;
        $stat->type = $type;
        $stat->description = $description;
        $stat->instance_name = $instance_name;
        $stat->status = $status;
        // Save
        if ($stat->save()) {
            return $stat;
        }
        // Fail
        return false;
    }

    public static function updateStatus($instance_name, $status, $type, $description)
    {
        $env = Static::findOne(['instance_name' => $instance_name]);
        $env->description = $description;
        $env->instance_name = $instance_name;
        $env->status = $status;
        if ($env->save()) {
            return $env;
        }
        return false;
    }

    public static function isExist($instance_name, $type)
    {
        if (Static::findOne(['instance_name' => $instance_name, 'type' => $type]) !== null) {
            return true;
        }
        return false;
    }

    /**
     * @param instance_name : string
     * @param type : string ('fe', 'sql')
     */
    public static function getStatus($instance_name, $type)
    {
        return InstanceStatus::findOne(['instance_name' => $instance_name, 'type' => $type]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Type',
            'description' => 'Description',
            'instance_name' => 'Instance Name',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}