<?php

use yii\db\Migration;

/**
 * Handles the creation of table `config`.
 */
class m170921_114241_create_config_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('config', [
            'id' => $this->primaryKey(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('config');
    }
}
