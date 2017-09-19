<?php

use yii\db\Migration;

class m170908_070341_create_table_article extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%article}}', [
            'article_id' => $this->integer(8)->unsigned()->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'cat_id' => $this->smallInteger(5)->notNull()->defaultValue('0'),
            'title' => $this->string(150)->notNull()->defaultValue(''),
            'content' => $this->text()->notNull(),
            'author' => $this->string(30)->notNull()->defaultValue(''),
            'author_email' => $this->string(60)->notNull()->defaultValue(''),
            'keywords' => $this->string(255)->notNull()->defaultValue(''),
            'article_type' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue('2'),
            'is_open' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue('1'),
            'add_time' => $this->integer(10)->unsigned()->notNull()->defaultValue('0'),
            'file_url' => $this->string(255)->notNull()->defaultValue(''),
            'open_type' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue('0'),
            'link' => $this->string(255)->notNull()->defaultValue(''),
            'description' => $this->string(255),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%article}}');
    }
}
