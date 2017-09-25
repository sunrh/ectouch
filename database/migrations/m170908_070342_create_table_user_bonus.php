<?php

use yii\db\Migration;

class m170908_070342_create_table_user_bonus extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%user_bonus}}', [
            'bonus_id' => $this->integer(8)->unsigned()->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'bonus_type_id' => $this->smallInteger(3)->unsigned()->notNull()->defaultValue('0'),
            'bonus_sn' => $this->bigInteger(20)->unsigned()->notNull()->defaultValue('0'),
            'user_id' => $this->integer(8)->unsigned()->notNull()->defaultValue('0'),
            'used_time' => $this->integer(10)->unsigned()->notNull()->defaultValue('0'),
            'order_id' => $this->integer(8)->unsigned()->notNull()->defaultValue('0'),
            'emailed' => $this->smallInteger(3)->unsigned()->notNull()->defaultValue('0'),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%user_bonus}}');
    }
}
