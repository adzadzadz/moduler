<?php

namespace console\models\middleware;

use Yii;
use console\models\middleware\Dbs;


/**
 * This is the model class for table "{{%connections}}".
 *
 * @property integer $connection_id
 * @property integer $config_id
 * @property integer $build_no
 * @property string $name
 * @property string $db_type
 * @property string $host
 * @property string $password
 */
class Connections extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%connections}}';
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
            [['environment_id', 'connection_id', 'name', 'db_type', 'host'], 'required'],
            [['environment_id', 'name', 'db_type', 'host', 'password'], 'string', 'max' => 255],
            [['connection_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'connection_id' => 'Connection ID',
            'environment_id' => 'Environment ID',
            'config_id' => 'Config ID',
            'build_no' => 'Build No',
            'name' => 'Name',
            'db_type' => 'Db Type',
            'host' => 'Host',
            'password' => 'Password',
        ];
    }
}
