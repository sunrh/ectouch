<?php

use yii\db\Migration;

class m170908_070341_create_table_error_log extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%error_log}}', [
            'id' => $this->integer(10)->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'info' => $this->string(255)->notNull(),
            'file' => $this->string(100)->notNull(),
            'time' => $this->integer(10)->notNull(),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%error_log}}');
    }
}
