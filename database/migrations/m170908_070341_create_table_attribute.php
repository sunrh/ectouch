<?php

use yii\db\Migration;

class m170908_070341_create_table_attribute extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%attribute}}', [
            'attr_id' => $this->smallInteger(5)->unsigned()->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'cat_id' => $this->smallInteger(5)->unsigned()->notNull()->defaultValue('0'),
            'attr_name' => $this->string(60)->notNull()->defaultValue(''),
            'attr_input_type' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue('1'),
            'attr_type' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue('1'),
            'attr_values' => $this->text()->notNull(),
            'attr_index' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue('0'),
            'sort_order' => $this->smallInteger(3)->unsigned()->notNull()->defaultValue('0'),
            'is_linked' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue('0'),
            'attr_group' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue('0'),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%attribute}}');
    }
}
