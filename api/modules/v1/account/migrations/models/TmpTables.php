<?php 

namespace api\modules\v1\account\migrations;

use Yii;
use yii\db\Command;
use yii\db\Schema;

class TmpTables extends \yii\base\Object
{
    public $version = '0.0.1';

    /**
     * @property $db | String
     * Yii::$app->strepzDbManager->getFncDb();
     */
    public $db;
    public $company_id;

    public function getDb()
    {
        return Yii::$app->get($this->db);
    }

    public function up()
    {
        $this->upUser();
        $this->upCompany();

        return true;
    }

    private function checkCompanyId()
    {
        if ($this->company_id !== false) {
            $glbUser = \common\models\GlbUser::findOne(['company_id' => $this->company_id]);
            return $glbUser;
        }
        return $this->company_id;
    }

    private function upUser()
    {
        do {
            $this->company_id = rand(10000000,99999999);
            $exist = $this->checkCompanyId();
        } while ($exist !== null);

        $db = $this->getDb();
        $db->createCommand()->createTable( '{{%' . $this->company_id . '_user}}' , [
            'user_id' => Schema::TYPE_PK,
            '_company_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            '_registration_token' => Schema::TYPE_STRING . ' NOT NULL',
            '_access_token' => Schema::TYPE_STRING . ' NOT NULL',
            '_region' => Schema::TYPE_STRING . ' NOT NULL',
            '_ipaddress' => Schema::TYPE_STRING . ' NOT NULL',

            'username' => Schema::TYPE_STRING . ' NOT NULL',
            'auth_key' => Schema::TYPE_STRING . '(32) NOT NULL',
            'password_hash' => Schema::TYPE_STRING . ' NOT NULL',
            'password_reset_token' => Schema::TYPE_STRING,
            'email' => Schema::TYPE_STRING . ' NOT NULL',
            'language' => Schema::TYPE_STRING . ' NOT NULL default ""',

            'firstname' => Schema::TYPE_STRING . ' NOT NULL default ""',
            'middlename' => Schema::TYPE_STRING . ' NOT NULL default ""',
            'lastname' => Schema::TYPE_STRING . ' NOT NULL default ""',

            'verification_code' => Schema::TYPE_STRING . ' NOT NULL',
            'status' => Schema::TYPE_SMALLINT . ' NOT NULL',
            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL',

        ])->execute();
    }

    private function upCompany()
    {
        $db = $this->getDb();
        $db->createCommand()->createTable( '{{%' . $this->company_id . '_company}}', [
            'id' => Schema::TYPE_PK,
            'name' => Schema::TYPE_STRING . ' NOT NULL default ""',
            'address' => Schema::TYPE_STRING . ' NOT NULL default ""',
            'postal_code' => Schema::TYPE_STRING . ' NOT NULL default ""',
            'city' => Schema::TYPE_STRING . ' NOT NULL default ""',
            'state' => Schema::TYPE_STRING . ' NOT NULL default ""',
            'country' => Schema::TYPE_STRING . ' NOT NULL default ""',
            'phone' => Schema::TYPE_STRING . ' NOT NULL default ""',
            'fax' => Schema::TYPE_STRING . ' NOT NULL default ""',
            'email' => Schema::TYPE_STRING . ' NOT NULL default ""',
            'website' => Schema::TYPE_STRING . ' NOT NULL default ""',

            'type' => Schema::TYPE_STRING . ' NOT NULL default ""',
            'size' => Schema::TYPE_STRING . ' NOT NULL default ""',

            'status' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 10',
            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL',
        ])->execute();
    }
}