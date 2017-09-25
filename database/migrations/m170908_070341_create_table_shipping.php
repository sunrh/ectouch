<?php

use yii\db\Migration;

class m170908_070341_create_table_shipping extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%shipping}}', [
            'shipping_id' => $this->smallInteger(3)->unsigned()->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'shipping_code' => $this->string(20)->notNull()->defaultValue(''),
            'shipping_name' => $this->string(120)->notNull()->defaultValue(''),
            'shipping_desc' => $this->string(255)->notNull()->defaultValue(''),
            'insure' => $this->string(10)->notNull()->defaultValue('0'),
            'support_cod' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue('0'),
            'enabled' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue('0'),
            'shipping_print' => $this->text()->notNull(),
            'print_bg' => $this->string(255),
            'config_lable' => $this->text(),
            'print_model' => $this->smallInteger(1)->defaultValue('0'),
            'shipping_order' => $this->smallInteger(3)->unsigned()->notNull()->defaultValue('0'),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%shipping}}');
    }
}
