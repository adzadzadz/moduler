<?php

namespace api\modules\v1\account\models;

use Yii;
use yii\base\NotSupportedException;
use yii\web\IdentityInterface;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\db\Command;
use yii\db\Schema;
use yii\web\Session;

class FncUserMeta extends ActiveRecord
{

    public static function getDb()
    {
        return Yii::$app->strepzDbManager->getFncDb();
    }

    public static function tableName()
    {
        return '{{%' . Yii::$app->strepzConfig->company_id . '_user_meta}}';
    }

    public function rules()
    {
        return [
            [['firstname', 'middlename', 'lastname'], 'string', 'min' => '2', 'max' => 64],
            [['mobile', 'phone'], 'integer']
        ];
    }

    /**
     * This is where tables are initialized. All required tables should be added here.
     * @return Boolean
     */
    public function initTables()
    {
        return $this->createUserMetaTableSchema();
    }

    private function createUserMetaTableSchema()
    {
        $db = $this->getDb();
        $result = $db->createCommand()->createTable( static::tableName() , [
            'user_id' => Schema::TYPE_INTEGER,
            'firstname' => Schema::TYPE_STRING . ' NOT NULL',
            'middlename' => Schema::TYPE_STRING . ' NOT NULL',
            'lastname' => Schema::TYPE_STRING . ' NOT NULL',
            'mobile' => Schema::TYPE_INTEGER . ' NOT NULL',
            'phone' => Schema::TYPE_INTEGER . ' NOT NULL',
        ])->execute();

        $this->createIndex ( 'meta_uid_ix', 'static::tableName()', ['user_id'] );
        $db->createCommand()->addPrimaryKey( 'pk_company_id', static::tableName(), ['user_id'] );
        $db->createCommand()->addForeignKey( 
            'fk_user_id', 
            $this->tablename(), 
            'user_id', 
            '{{%' . Yii::$app->strepzConfig->company_id . '_user}}', 
            'id', 
            $delete = 'CASCADE',
            $update = null );

        // 0 means success in this section. 
        if ($result === 0) {
            return true;
        }
        return false;
    }

}