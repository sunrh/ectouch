<?php

use yii\db\Migration;

class m170908_070341_create_table_order_action extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%order_action}}', [
            'action_id' => $this->integer(8)->unsigned()->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'order_id' => $this->integer(8)->unsigned()->notNull()->defaultValue('0'),
            'action_user' => $this->string(30)->notNull()->defaultValue(''),
            'order_status' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue('0'),
            'shipping_status' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue('0'),
            'pay_status' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue('0'),
            'action_place' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue('0'),
            'action_note' => $this->string(255)->notNull()->defaultValue(''),
            'log_time' => $this->integer(11)->unsigned()->notNull()->defaultValue('0'),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%order_action}}');
    }
}
