<?php

use yii\db\Migration;

class m170908_070341_create_table_exchange_goods extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%exchange_goods}}', [
            'goods_id' => $this->integer(8)->unsigned()->notNull()->append('PRIMARY KEY'),
            'exchange_integral' => $this->integer(10)->unsigned()->notNull()->defaultValue('0'),
            'is_exchange' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue('0'),
            'is_hot' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue('0'),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%exchange_goods}}');
    }
}
