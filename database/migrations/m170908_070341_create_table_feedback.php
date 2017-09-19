<?php

use yii\db\Migration;

class m170908_070341_create_table_feedback extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%feedback}}', [
            'msg_id' => $this->integer(8)->unsigned()->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'parent_id' => $this->integer(8)->unsigned()->notNull()->defaultValue('0'),
            'user_id' => $this->integer(8)->unsigned()->notNull()->defaultValue('0'),
            'user_name' => $this->string(60)->notNull()->defaultValue(''),
            'user_email' => $this->string(60)->notNull()->defaultValue(''),
            'msg_title' => $this->string(200)->notNull()->defaultValue(''),
            'msg_type' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue('0'),
            'msg_status' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue('0'),
            'msg_content' => $this->text()->notNull(),
            'msg_time' => $this->integer(10)->unsigned()->notNull()->defaultValue('0'),
            'message_img' => $this->string(255)->notNull()->defaultValue('0'),
            'order_id' => $this->integer(11)->unsigned()->notNull()->defaultValue('0'),
            'msg_area' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue('0'),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%feedback}}');
    }
}
