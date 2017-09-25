<?php

use yii\db\Migration;

/**
 * Handles the creation of table `company`.
 */
class m170921_114212_create_company_table extends Migration
{
    public $company_id;

    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('{{company}}', [
            'id' => $this->primaryKey(),
            'name' => Schema::TYPE_STRING . ' NOT NULL',
            'address' => Schema::TYPE_STRING . ' NOT NULL',
            'postal_code' => Schema::TYPE_STRING . ' NOT NULL',
            'city' => Schema::TYPE_STRING . ' NOT NULL',
            'state' => Schema::TYPE_STRING . ' NOT NULL',
            'country' => Schema::TYPE_STRING . ' NOT NULL',
            'phone' => Schema::TYPE_STRING . ' NOT NULL',
            'fax' => Schema::TYPE_STRING . ' NOT NULL',
            'email' => Schema::TYPE_STRING . ' NOT NULL',
            'website' => Schema::TYPE_STRING . ' NOT NULL',

            'type' => Schema::TYPE_STRING . ' NOT NULL',
            'size' => Schema::TYPE_STRING . ' NOT NULL',

            'status' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 10',
            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('company');
    }
}
