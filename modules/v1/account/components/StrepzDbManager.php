<?php

namespace common\components;

use Yii;
use yii\db\Command;
use yii\db\Schema;
use yii\base\Object;
use common\models\GlbDbBalancer;
use yii\di\Instance;

class StrepzDbManager extends Object
{
    public $db_id;

    public function init()
    {
        parent::init();
    }

    public function getFncDb()
    {
        if ($this->db_id == null) {
            $db_id = 'fnc_db_01'; #Yii::$app->session->get('fnc_db');
        }
        return Yii::$app->get($db_id);
    }

    public function setDbId($db)
    {
        $this->db_id = $db;
        return true;
    }

    /*
     * @method getSelectedDb() requires review for removal
     *
     **/
    private function getSelectedDb()
    {
        $db = GlbDbBalancer::findOne([
            'db_id' => $this->db_id,
        ]);

        $locator = new \yii\di\ServiceLocator;
        $locator->set($this->db_id, [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=' . $db->host . ';dbname=' . $db->db_name . '',
            'username' => $db->host_username,
            'password' => $db->host_password,
            'tablePrefix' => $db->table_prefix,
            'charset' => 'utf8',
        ]);

        return $locator->get($this->db_id);
    }

    // Selects the DB with lowest load
    public function selectDb($db_id = null)
    {
        if ($db_id !== null) {
            $this->db_id = $db_id;
        }
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