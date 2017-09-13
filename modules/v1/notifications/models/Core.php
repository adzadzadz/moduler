<?php

namespace api\modules\v1\notifications\models;

use Yii;

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
class Core extends \yii\base\Model
{
    public static function fetch()
    {
        return Yii::$app->controller->module->db;
    }
}
