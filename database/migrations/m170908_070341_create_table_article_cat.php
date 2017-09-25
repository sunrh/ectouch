<?php

use yii\db\Migration;

class m170908_070341_create_table_article_cat extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%article_cat}}', [
            'cat_id' => $this->smallInteger(5)->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'cat_name' => $this->string(255)->notNull()->defaultValue(''),
            'cat_type' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue('1'),
            'keywords' => $this->string(255)->notNull()->defaultValue(''),
            'cat_desc' => $this->string(255)->notNull()->defaultValue(''),
            'sort_order' => $this->smallInteger(3)->unsigned()->notNull()->defaultValue('50'),
            'show_in_nav' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue('0'),
            'parent_id' => $this->smallInteger(5)->unsigned()->notNull()->defaultValue('0'),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%article_cat}}');
    }
}
