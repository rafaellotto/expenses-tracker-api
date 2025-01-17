<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user}}`.
 */
class m250115_005554_create_user_table extends Migration
{
    public function safeUp(): void
    {
        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey(),
            'email' => $this->string()->notNull()->unique(),
            'password' => $this->string()->notNull(),
            'created_at' => $this->dateTime()->notNull(),
        ]);
    }

    public function safeDown(): void
    {
        $this->dropTable('{{%user}}');
    }
}
