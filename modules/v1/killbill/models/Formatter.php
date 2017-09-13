<?php 

namespace app\modules\v1\killbill\models;

class Formatter extends \yii\base\Object
{
    public static function get($obj, $props = [])
    {   
        $data = [];
        foreach ($props as $prop) {
            $data[$prop] = $obj->$prop;
        }
        return $data;
    }
}