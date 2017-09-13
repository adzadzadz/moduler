<?php
namespace console\modules\frontendcontroller\models;

use Yii;

class ConfigBuilder extends \yii\base\Model
{
	/** 
     * Writes the correct data for the config files
     * This includes writing the database components (no longer apply)
     */ 
    public function write($filename, $content, $path = null)
    {
        $file = fopen($path !== null ? $path . "/$filename" : Yii::getAlias("common/config/strepz/$filename"), "w") or die("Unable to open common/config/strepz/$filename!");
        fwrite($file, $content);
        
        if (fclose($file)) {
            return true;
        }
        return false;
    }
}