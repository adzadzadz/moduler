<?php

use yii\db\Migration;

/**
 * Handles the creation of table `logs`.
 */
class m170921_114249_create_logs_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('logs', [
            'id' => $this->primaryKey(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('logs');
    }
}
