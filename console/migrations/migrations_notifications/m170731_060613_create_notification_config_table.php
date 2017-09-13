<?php

use yii\db\Migration;

/**
 * Handles the creation of table `notification_config`.
 */
class m170731_060613_create_notification_config_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('{{%notification_config}}', [
            'id' => $this->primaryKey(),
            'type' => $this->string()->notNull(),
            'value' => $this->string()->notNull(),

            'status' => $this->smallInteger()->defaultValue(10),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'author_id' => $this->integer()->notNull(),
            'updater_id' => $this->integer()->notNull(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%notification_config}}');
    }
}