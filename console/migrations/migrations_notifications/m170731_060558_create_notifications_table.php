<?php

use yii\db\Migration;

/**
 * Handles the creation of table `notifications`.
 */
class m170731_060558_create_notifications_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('{{%notifications}}', [
            'id' => $this->primaryKey(),
            'scope' => $this->string()->notNull(),
            'type' => $this->string()->notNull(),
            'subject' => $this->string()->notNull(),
            'text' => $this->string()->notNull(),
            'schedule' => $this->integer()->defaultValue(0),

            'status'     => $this->smallInteger()->defaultValue(10),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            // 'author_id'  => $this->integer()->notNull(),
            // 'updater_id' => $this->integer()->notNull(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%notifications}}');
    }
}