<?php

namespace console\models\middleware;

use Yii;

/**
 * This is the model class for table "{{%dbs}}".
 *
 * @property integer $row_id
 * @property string $db_id
 * @property string $db_type
 * @property integer $connection_id
 * @property string $tbl_prefix
 */
class Dbs extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%dbs}}';
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
            [['environment_id', 'db_id', 'db_type', 'connection_id', 'tbl_prefix'], 'required'],
            [['connection_id'], 'integer'],
            [['environment_id', 'db_id', 'db_type', 'tbl_prefix'], 'string', 'max' => 255],
            // [['db_id'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'row_id' => 'Row ID',
            'environment_id' => 'Environment ID',
            'db_id' => 'Db ID',
            'db_type' => 'Db Type',
            'connection_id' => 'Connection ID',
            'tbl_prefix' => 'Tbl Prefix',
        ];
    }
}
