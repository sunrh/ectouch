<?php

use yii\db\Migration;

class m170908_070341_create_table_category extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%category}}', [
            'cat_id' => $this->smallInteger(5)->unsigned()->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'cat_name' => $this->string(90)->notNull()->defaultValue(''),
            'keywords' => $this->string(255)->notNull()->defaultValue(''),
            'cat_desc' => $this->string(255)->notNull()->defaultValue(''),
            'parent_id' => $this->smallInteger(5)->unsigned()->notNull()->defaultValue('0'),
            'sort_order' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue('50'),
            'template_file' => $this->string(50)->notNull()->defaultValue(''),
            'measure_unit' => $this->string(15)->notNull()->defaultValue(''),
            'show_in_nav' => $this->smallInteger(1)->notNull()->defaultValue('0'),
            'style' => $this->string(150)->notNull(),
            'is_show' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue('1'),
            'grade' => $this->smallInteger(4)->notNull()->defaultValue('0'),
            'filter_attr' => $this->string(255)->notNull()->defaultValue('0'),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%category}}');
    }
}
