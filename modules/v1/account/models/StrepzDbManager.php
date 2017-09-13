<?php

namespace modules\v1\account\models;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\db\ActiveQuery;
use yii\db\Command;
use yii\db\Schema;
use modules\v1\account\models\GlbDbBalancer;


class StrepzDbManager extends ActiveRecord
{
    public static function getFncDb()
    {
        return StrepzDbManager::getSelectedDb();
    }

    private static function getSelectedDb()
    {
        $db_id = Yii::$app->session->get('fnc_db');
        $db = GlbDbBalancer::findOne([
            'db_id' => $db_id,
        ]);

        $locator = new \yii\di\ServiceLocator;
        $locator->set($db_id, [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=' . $db->host . ';dbname=' . $db->db_name . '',
            'username' => $db->host_username,
            'password' => $db->host_password,
            'tablePrefix' => $db->table_prefix,
            'charset' => 'utf8',
        ]);

        return $locator->get($db_id);
    }

    // Selects the DB with lowest load
    public static function selectDb()
    {
        $dbBalancer = GlbDbBalancer::find()
            ->where(['db_current_load' => GlbDbBalancer::find()->min('db_current_load')])
            ->one();

        $db_update = GlbDbBalancer::findOne(['db_id' => $dbBalancer->db_id]);
        $db_update->db_current_load = $db_update->db_current_load + 1;

        if ($db_update->save()) {
            return $dbBalancer->db_id;
        }
        return null;
    }

}