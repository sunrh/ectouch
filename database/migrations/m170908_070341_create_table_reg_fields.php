<?php

use yii\db\Migration;

class m170908_070341_create_table_reg_fields extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%reg_fields}}', [
            'id' => $this->smallInteger(3)->unsigned()->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'reg_field_name' => $this->string(60)->notNull(),
            'dis_order' => $this->smallInteger(3)->unsigned()->notNull()->defaultValue('100'),
            'display' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue('1'),
            'type' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue('0'),
            'is_need' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue('1'),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%reg_fields}}');
    }
}
