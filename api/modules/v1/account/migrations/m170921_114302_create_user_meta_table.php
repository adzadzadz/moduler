<?php

use yii\db\Migration;

/**
 * Handles the creation of table `user_meta`.
 */
class m170921_114302_create_user_meta_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('user_meta', [
            'id' => $this->primaryKey(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('user_meta');
    }
}
