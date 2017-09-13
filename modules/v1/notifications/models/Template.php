<?php

namespace api\modules\v1\notifications\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;

/**
 * This is the model class for table "{{%template}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $content
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 */
class Template extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return '{{%template}}';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get(Yii::$app->controller->module->db);
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
            // [
            //     'class' => BlameableBehavior::className(),
            //     'createdByAttribute' => 'author_id',
            //     'updatedByAttribute' => 'updater_id',
            // ],
        ];
    }

    public function rules()
    {
        return [
            [['name', 'content'], 'required'],
            [['status', 'created_at', 'updated_at'], 'integer'],
            ['name', 'unique'],
            
            ['status', 'default', 'value' => 10],
            [['name', 'content'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'content' => 'Content',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
