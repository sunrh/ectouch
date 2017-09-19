<?php

use yii\db\Migration;

class m170908_070341_create_table_email_list extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%email_list}}', [
            'id' => $this->integer(8)->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'email' => $this->string(60)->notNull(),
            'stat' => $this->smallInteger(1)->notNull()->defaultValue('0'),
            'hash' => $this->string(10)->notNull(),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%email_list}}');
    }
}
