<?php

use yii\db\Migration;

class m170908_070341_create_table_comment extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%comment}}', [
            'comment_id' => $this->integer(10)->unsigned()->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'comment_type' => $this->smallInteger(3)->unsigned()->notNull()->defaultValue('0'),
            'id_value' => $this->integer(8)->unsigned()->notNull()->defaultValue('0'),
            'email' => $this->string(60)->notNull()->defaultValue(''),
            'user_name' => $this->string(60)->notNull()->defaultValue(''),
            'content' => $this->text()->notNull(),
            'comment_rank' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue('0'),
            'add_time' => $this->integer(10)->unsigned()->notNull()->defaultValue('0'),
            'ip_address' => $this->string(15)->notNull()->defaultValue(''),
            'status' => $this->smallInteger(3)->unsigned()->notNull()->defaultValue('0'),
            'parent_id' => $this->integer(10)->unsigned()->notNull()->defaultValue('0'),
            'user_id' => $this->integer(10)->unsigned()->notNull()->defaultValue('0'),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%comment}}');
    }
}
