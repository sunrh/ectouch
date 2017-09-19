<?php

use yii\db\Migration;

class m170908_070341_create_table_topic extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%topic}}', [
            'topic_id' => $this->integer(10)->unsigned()->notNull(),
            'title' => $this->string(255)->notNull()->defaultValue(''''),
            'intro' => $this->text()->notNull(),
            'start_time' => $this->integer(11)->notNull()->defaultValue('0'),
            'end_time' => $this->integer(10)->notNull()->defaultValue('0'),
            'data' => $this->text()->notNull(),
            'template' => $this->string(255)->notNull()->defaultValue(''''),
            'css' => $this->text()->notNull(),
            'topic_img' => $this->string(255),
            'title_pic' => $this->string(255),
            'base_style' => $this->char(6),
            'htmls' => $this->text(),
            'keywords' => $this->string(255),
            'description' => $this->string(255),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%topic}}');
    }
}
