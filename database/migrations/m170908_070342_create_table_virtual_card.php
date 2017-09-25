<?php

use yii\db\Migration;

class m170908_070342_create_table_virtual_card extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%virtual_card}}', [
            'card_id' => $this->integer(8)->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'goods_id' => $this->integer(8)->unsigned()->notNull()->defaultValue('0'),
            'card_sn' => $this->string(60)->notNull()->defaultValue(''),
            'card_password' => $this->string(60)->notNull()->defaultValue(''),
            'add_date' => $this->integer(11)->notNull()->defaultValue('0'),
            'end_date' => $this->integer(11)->notNull()->defaultValue('0'),
            'is_saled' => $this->smallInteger(1)->notNull()->defaultValue('0'),
            'order_sn' => $this->string(20)->notNull()->defaultValue(''),
            'crc32' => $this->string(12)->notNull()->defaultValue('0'),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%virtual_card}}');
    }
}
