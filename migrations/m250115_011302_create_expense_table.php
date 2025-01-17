<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%expense}}`.
 */
class m250115_011302_create_expense_table extends Migration
{
    public function safeUp(): void
    {
        $this->createTable('{{%expense}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'description' => $this->string()->notNull(),
            'category' => "ENUM('Alimentação', 'Transporte', 'Lazer') NOT NULL",
            'amount' => $this->decimal(10, 2)->unsigned()->notNull(),
            'date' => $this->date()->notNull(),
            'created_at' => $this->dateTime()->notNull(),
            'updated_at' => $this->dateTime()->notNull(),
        ]);

        $this->addForeignKey(
            name: 'expense_user_id_fk',
            table: '{{%expense}}',
            columns: 'user_id',
            refTable: '{{%user}}',
            refColumns: 'id',
            delete: 'CASCADE',
        );
    }

    public function safeDown(): void
    {
        $this->dropForeignKey('expense_user_id_fk', '{{%expense}}');
        $this->dropTable('{{%expense}}');
    }
}
