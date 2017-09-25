<?php

use yii\db\Migration;

class m170908_070341_create_table_back_goods extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%back_goods}}', [
            'rec_id' => $this->integer(8)->unsigned()->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'back_id' => $this->integer(8)->unsigned()->defaultValue('0'),
            'goods_id' => $this->integer(8)->unsigned()->notNull()->defaultValue('0'),
            'product_id' => $this->integer(8)->unsigned()->notNull()->defaultValue('0'),
            'product_sn' => $this->string(60),
            'goods_name' => $this->string(120),
            'brand_name' => $this->string(60),
            'goods_sn' => $this->string(60),
            'is_real' => $this->smallInteger(1)->unsigned()->defaultValue('0'),
            'send_number' => $this->smallInteger(5)->unsigned()->defaultValue('0'),
            'goods_attr' => $this->text(),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%back_goods}}');
    }
}
