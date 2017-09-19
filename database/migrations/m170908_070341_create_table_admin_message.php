<?php

use yii\db\Migration;

class m170908_070341_create_table_admin_message extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%admin_message}}', [
            'message_id' => $this->smallInteger(5)->unsigned()->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'sender_id' => $this->smallInteger(3)->unsigned()->notNull()->defaultValue('0'),
            'receiver_id' => $this->smallInteger(3)->unsigned()->notNull()->defaultValue('0'),
            'sent_time' => $this->integer(11)->unsigned()->notNull()->defaultValue('0'),
            'read_time' => $this->integer(11)->unsigned()->notNull()->defaultValue('0'),
            'readed' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue('0'),
            'deleted' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue('0'),
            'title' => $this->string(150)->notNull()->defaultValue(''),
            'message' => $this->text()->notNull(),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%admin_message}}');
    }
}
