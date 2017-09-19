<?php

use yii\db\Migration;

class m170908_070341_create_table_reg_extend_info extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%reg_extend_info}}', [
            'Id' => $this->integer(10)->unsigned()->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'user_id' => $this->integer(8)->unsigned()->notNull(),
            'reg_field_id' => $this->integer(10)->unsigned()->notNull(),
            'content' => $this->text()->notNull(),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%reg_extend_info}}');
    }
}
