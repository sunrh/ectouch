<?php

use yii\db\Migration;

class m170908_070341_create_table_member_price extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%member_price}}', [
            'price_id' => $this->integer(8)->unsigned()->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'goods_id' => $this->integer(8)->unsigned()->notNull()->defaultValue('0'),
            'user_rank' => $this->smallInteger(3)->notNull()->defaultValue('0'),
            'user_price' => $this->decimal(10,2)->notNull()->defaultValue('0.00'),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%member_price}}');
    }
}
