<?php

use yii\db\Migration;

class m170908_070341_create_table_bonus_type extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%bonus_type}}', [
            'type_id' => $this->smallInteger(5)->unsigned()->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'type_name' => $this->string(60)->notNull()->defaultValue(''),
            'type_money' => $this->decimal(10,2)->notNull()->defaultValue('0.00'),
            'send_type' => $this->smallInteger(3)->unsigned()->notNull()->defaultValue('0'),
            'min_amount' => $this->decimal(10,2)->unsigned()->notNull()->defaultValue('0.00'),
            'max_amount' => $this->decimal(10,2)->unsigned()->notNull()->defaultValue('0.00'),
            'send_start_date' => $this->integer(11)->notNull()->defaultValue('0'),
            'send_end_date' => $this->integer(11)->notNull()->defaultValue('0'),
            'use_start_date' => $this->integer(11)->notNull()->defaultValue('0'),
            'use_end_date' => $this->integer(11)->notNull()->defaultValue('0'),
            'min_goods_amount' => $this->decimal(10,2)->unsigned()->notNull()->defaultValue('0.00'),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%bonus_type}}');
    }
}
