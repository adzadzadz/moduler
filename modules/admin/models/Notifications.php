<?php

namespace api\modules\admin\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;

/**
 * This is the model class for table "{{%notifications}}".
 *
 * @property integer $id
 * @property string $scope
 * @property string $type
 * @property string $subject
 * @property string $text
 * @property integer $schedule
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $author_id
 * @property integer $updater_id
 */
class Notifications extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%notifications}}';
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

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('glb_ntf_db_01');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['scope', 'type', 'subject', 'text',], 'required'],
            [['schedule', 'status', 'created_at', 'updated_at'], 'integer'],
            [['scope', 'type', 'subject', 'text'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'scope' => 'Scope',
            'type' => 'Type',
            'subject' => 'Subject',
            'text' => 'Text',
            'schedule' => 'Schedule',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            // 'author_id' => 'Author ID',
            // 'updater_id' => 'Updater ID',
        ];
    }
}
