<?php

namespace console\models\middleware;

use Yii;

/**
 * This is the model class for table "{{%user_auth}}".
 *
 * @property string $user_id
 * @property string $source
 * @property string $source_id
 */
class EnvironmentData extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%environment_data}}';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('miw_db_01');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['environment_id', 'data_key', 'data_value'], 'required'],
            [['environment_id', 'data_key', 'data_value'], 'string', 'max' => 255],
            [['data_value'], 'default', 'value' => ''],
        ];
    }

    public static function getData($environment_id)
    {
        $envData = EnvironmentData::findAll(['environment_id' => $environment_id]);

        $data = [];
        foreach ($envData as $each) {
            $data[$each->data_key] = $each->data_value;
        }
        return $data;
    }

        /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'environment_id' => 'environment_id',
            'db_id' => 'db_id',
            'data_key' => 'data_key',
            'data_value' => 'data_value',
        ];
    }
}