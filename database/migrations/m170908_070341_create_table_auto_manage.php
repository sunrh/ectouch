<?php

use yii\db\Migration;

class m170908_070341_create_table_auto_manage extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%auto_manage}}', [
            'item_id' => $this->integer(8)->notNull(),
            'type' => $this->string(10)->notNull(),
            'starttime' => $this->integer(10)->notNull(),
            'endtime' => $this->integer(10)->notNull(),
        ], $tableOptions);

        $this->addPrimaryKey('primary_key', '{{%auto_manage}}', ['item_id','type']);
    }

    public function safeDown()
    {
        $this->dropTable('{{%auto_manage}}');
    }
}
