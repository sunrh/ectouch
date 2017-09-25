<?php

use yii\db\Migration;

class m170908_070341_create_table_pay_log extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%pay_log}}', [
            'log_id' => $this->integer(10)->unsigned()->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'order_id' => $this->integer(8)->unsigned()->notNull()->defaultValue('0'),
            'order_amount' => $this->decimal(10,2)->unsigned()->notNull(),
            'order_type' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue('0'),
            'is_paid' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue('0'),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%pay_log}}');
    }
}
