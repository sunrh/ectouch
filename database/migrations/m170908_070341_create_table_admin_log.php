<?php

use yii\db\Migration;

class m170908_070341_create_table_admin_log extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%admin_log}}', [
            'log_id' => $this->integer(10)->unsigned()->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'log_time' => $this->integer(10)->unsigned()->notNull()->defaultValue('0'),
            'user_id' => $this->smallInteger(3)->unsigned()->notNull()->defaultValue('0'),
            'log_info' => $this->string(255)->notNull()->defaultValue(''),
            'ip_address' => $this->string(15)->notNull()->defaultValue(''),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%admin_log}}');
    }
}
