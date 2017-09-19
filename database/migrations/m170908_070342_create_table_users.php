<?php

use yii\db\Migration;

class m170908_070342_create_table_users extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%users}}', [
            'user_id' => $this->integer(8)->unsigned()->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'email' => $this->string(60)->notNull()->defaultValue(''),
            'user_name' => $this->string(60)->notNull()->defaultValue(''),
            'password' => $this->string(32)->notNull()->defaultValue(''),
            'question' => $this->string(255)->notNull()->defaultValue(''),
            'answer' => $this->string(255)->notNull()->defaultValue(''),
            'sex' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue('0'),
            'birthday' => $this->date()->notNull()->defaultValue('1000-01-01'),
            'user_money' => $this->decimal(10,2)->notNull()->defaultValue('0.00'),
            'frozen_money' => $this->decimal(10,2)->notNull()->defaultValue('0.00'),
            'pay_points' => $this->integer(10)->unsigned()->notNull()->defaultValue('0'),
            'rank_points' => $this->integer(10)->unsigned()->notNull()->defaultValue('0'),
            'address_id' => $this->integer(8)->unsigned()->notNull()->defaultValue('0'),
            'reg_time' => $this->integer(10)->unsigned()->notNull()->defaultValue('0'),
            'last_login' => $this->integer(11)->unsigned()->notNull()->defaultValue('0'),
            'last_time' => $this->dateTime()->notNull()->defaultValue('1000-01-01 00:00:00'),
            'last_ip' => $this->string(15)->notNull()->defaultValue(''),
            'visit_count' => $this->smallInteger(5)->unsigned()->notNull()->defaultValue('0'),
            'user_rank' => $this->smallInteger(3)->unsigned()->notNull()->defaultValue('0'),
            'is_special' => $this->smallInteger(3)->unsigned()->notNull()->defaultValue('0'),
            'ec_salt' => $this->string(10),
            'salt' => $this->string(10)->notNull()->defaultValue('0'),
            'parent_id' => $this->integer(9)->notNull()->defaultValue('0'),
            'flag' => $this->smallInteger(3)->unsigned()->notNull()->defaultValue('0'),
            'alias' => $this->string(60)->notNull(),
            'msn' => $this->string(60)->notNull(),
            'qq' => $this->string(20)->notNull(),
            'office_phone' => $this->string(20)->notNull(),
            'home_phone' => $this->string(20)->notNull(),
            'mobile_phone' => $this->string(20)->notNull(),
            'is_validated' => $this->smallInteger(3)->unsigned()->notNull()->defaultValue('0'),
            'credit_line' => $this->decimal(10,2)->unsigned()->notNull(),
            'passwd_question' => $this->string(50),
            'passwd_answer' => $this->string(255),
        ], $tableOptions);

        $this->createIndex('user_name', '{{%users}}', 'user_name', true);
    }

    public function safeDown()
    {
        $this->dropTable('{{%users}}');
    }
}
