<?php

use yii\db\Migration;

class m170908_070341_create_table_keywords extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%keywords}}', [
            'date' => $this->date()->notNull(),
            'searchengine' => $this->string(20)->notNull(),
            'keyword' => $this->string(90)->notNull(),
            'count' => $this->integer(8)->unsigned()->notNull()->defaultValue('0'),
        ], $tableOptions);

        $this->addPrimaryKey('primary_key', '{{%keywords}}', ['date','searchengine','keyword']);
    }

    public function safeDown()
    {
        $this->dropTable('{{%keywords}}');
    }
}
