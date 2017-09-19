<?php

use yii\db\Migration;

class m170908_070341_create_table_affiliate_log extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%affiliate_log}}', [
            'log_id' => $this->integer(8)->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'order_id' => $this->integer(8)->notNull(),
            'time' => $this->integer(10)->notNull(),
            'user_id' => $this->integer(8)->notNull(),
            'user_name' => $this->string(60),
            'money' => $this->decimal(10,2)->notNull()->defaultValue('0.00'),
            'point' => $this->integer(10)->notNull()->defaultValue('0'),
            'separate_type' => $this->smallInteger(1)->notNull()->defaultValue('0'),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%affiliate_log}}');
    }
}
