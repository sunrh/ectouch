<?php

use yii\db\Migration;

class m170908_070341_create_table_shipping_area extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%shipping_area}}', [
            'shipping_area_id' => $this->smallInteger(5)->unsigned()->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'shipping_area_name' => $this->string(150)->notNull()->defaultValue(''),
            'shipping_id' => $this->smallInteger(3)->unsigned()->notNull()->defaultValue('0'),
            'configure' => $this->text()->notNull(),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%shipping_area}}');
    }
}
