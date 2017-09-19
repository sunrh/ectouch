<?php

use yii\db\Migration;

class m170908_070341_create_table_searchengine extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%searchengine}}', [
            'date' => $this->date()->notNull(),
            'searchengine' => $this->string(20)->notNull(),
            'count' => $this->integer(8)->unsigned()->notNull()->defaultValue('0'),
        ], $tableOptions);

        $this->addPrimaryKey('primary_key', '{{%searchengine}}', ['date','searchengine']);
    }

    public function safeDown()
    {
        $this->dropTable('{{%searchengine}}');
    }
}
