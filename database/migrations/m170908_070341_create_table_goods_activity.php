<?php

use yii\db\Migration;

class m170908_070341_create_table_goods_activity extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%goods_activity}}', [
            'act_id' => $this->integer(8)->unsigned()->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'act_name' => $this->string(255)->notNull(),
            'act_desc' => $this->text()->notNull(),
            'act_type' => $this->smallInteger(3)->unsigned()->notNull(),
            'goods_id' => $this->integer(8)->unsigned()->notNull(),
            'product_id' => $this->integer(8)->unsigned()->notNull()->defaultValue('0'),
            'goods_name' => $this->string(255)->notNull(),
            'start_time' => $this->integer(10)->unsigned()->notNull(),
            'end_time' => $this->integer(10)->unsigned()->notNull(),
            'is_finished' => $this->smallInteger(3)->unsigned()->notNull(),
            'ext_info' => $this->text()->notNull(),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%goods_activity}}');
    }
}
