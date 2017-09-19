<?php

use yii\db\Migration;

class m170908_070341_create_table_goods_attr extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%goods_attr}}', [
            'goods_attr_id' => $this->integer(10)->unsigned()->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'goods_id' => $this->integer(8)->unsigned()->notNull()->defaultValue('0'),
            'attr_id' => $this->smallInteger(5)->unsigned()->notNull()->defaultValue('0'),
            'attr_value' => $this->text()->notNull(),
            'attr_price' => $this->string(255)->notNull()->defaultValue(''),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%goods_attr}}');
    }
}
