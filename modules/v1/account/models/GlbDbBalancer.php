<?php

namespace api\modules\v1\account\models;

use Yii;

/**
 * This is the model class for table "{{%fnc_db_balancer}}".
 *
 * @property integer $id
 * @property string $host
 * @property string $db_name
 * @property string $host_username
 * @property string $host_password
 * @property string $table_prefix
 * @property integer $db_load_limit
 * @property integer $db_current_load
 * @property integer $status
 */
class GlbDbBalancer extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%fnc_db_balancer}}';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('glb_cnf_db_01');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['db_id', 'db_current_load'], 'required'],
            [['db_load_limit', 'db_current_load', 'status'], 'integer'],
            ['db_id', 'string', 'max' => 255],

            ['db_id', 'unique', 'targetClass' => '\api\modules\v1\account\models\GlbDbBalancer', 'message' => 'This Db ID is already in use'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'db_id' => 'DB Id',
            'db_load_limit' => 'DB Load Limit',
            'db_current_load' => 'DB Current Load',
            'status' => 'Status',
        ];
    }
}
