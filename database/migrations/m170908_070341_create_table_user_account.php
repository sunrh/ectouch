<?php

use yii\db\Migration;

class m170908_070341_create_table_user_account extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%user_account}}', [
            'id' => $this->integer(8)->unsigned()->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'user_id' => $this->integer(8)->unsigned()->notNull()->defaultValue('0'),
            'admin_user' => $this->string(255)->notNull(),
            'amount' => $this->decimal(10,2)->notNull(),
            'add_time' => $this->integer(10)->notNull()->defaultValue('0'),
            'paid_time' => $this->integer(10)->notNull()->defaultValue('0'),
            'admin_note' => $this->string(255)->notNull(),
            'user_note' => $this->string(255)->notNull(),
            'process_type' => $this->smallInteger(1)->notNull()->defaultValue('0'),
            'payment' => $this->string(90)->notNull(),
            'is_paid' => $this->smallInteger(1)->notNull()->defaultValue('0'),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%user_account}}');
    }
}
