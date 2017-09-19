<?php

use yii\db\Migration;

class m170908_070341_create_table_account_log extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%account_log}}', [
            'log_id' => $this->integer(8)->unsigned()->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'user_id' => $this->integer(8)->unsigned()->notNull(),
            'user_money' => $this->decimal(10,2)->notNull(),
            'frozen_money' => $this->decimal(10,2)->notNull(),
            'rank_points' => $this->integer(9)->notNull(),
            'pay_points' => $this->integer(9)->notNull(),
            'change_time' => $this->integer(10)->unsigned()->notNull(),
            'change_desc' => $this->string(255)->notNull(),
            'change_type' => $this->smallInteger(3)->unsigned()->notNull(),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%account_log}}');
    }
}
