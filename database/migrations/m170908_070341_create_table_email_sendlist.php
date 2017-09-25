<?php

use yii\db\Migration;

class m170908_070341_create_table_email_sendlist extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%email_sendlist}}', [
            'id' => $this->integer(8)->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'email' => $this->string(100)->notNull(),
            'template_id' => $this->integer(8)->notNull(),
            'email_content' => $this->text()->notNull(),
            'error' => $this->smallInteger(1)->notNull()->defaultValue('0'),
            'pri' => $this->smallInteger(10)->notNull(),
            'last_send' => $this->integer(10)->notNull(),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%email_sendlist}}');
    }
}
