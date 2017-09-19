<?php

use yii\db\Migration;

class m170908_070341_create_table_admin_user extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%admin_user}}', [
            'user_id' => $this->smallInteger(5)->unsigned()->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'user_name' => $this->string(60)->notNull()->defaultValue(''),
            'email' => $this->string(60)->notNull()->defaultValue(''),
            'password' => $this->string(32)->notNull()->defaultValue(''),
            'ec_salt' => $this->string(10),
            'add_time' => $this->integer(11)->notNull()->defaultValue('0'),
            'last_login' => $this->integer(11)->notNull()->defaultValue('0'),
            'last_ip' => $this->string(15)->notNull()->defaultValue(''),
            'action_list' => $this->text()->notNull(),
            'nav_list' => $this->text()->notNull(),
            'lang_type' => $this->string(50)->notNull()->defaultValue(''),
            'agency_id' => $this->smallInteger(5)->unsigned()->notNull(),
            'suppliers_id' => $this->smallInteger(5)->unsigned()->defaultValue('0'),
            'todolist' => $this->text(),
            'role_id' => $this->smallInteger(5),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%admin_user}}');
    }
}
