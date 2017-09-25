<?php

use yii\db\Migration;

class m170908_070341_create_table_products extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%products}}', [
            'product_id' => $this->integer(8)->unsigned()->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'goods_id' => $this->integer(8)->unsigned()->notNull()->defaultValue('0'),
            'goods_attr' => $this->string(50),
            'product_sn' => $this->string(60),
            'product_number' => $this->smallInteger(5)->unsigned()->defaultValue('0'),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%products}}');
    }
}
