<?php

use yii\db\Migration;

class m170908_070341_create_table_sessions extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%sessions}}', [
            'sesskey' => $this->char(32)->notNull()->append('PRIMARY KEY'),
            'expiry' => $this->integer(10)->unsigned()->notNull()->defaultValue('0'),
            'userid' => $this->integer(8)->unsigned()->notNull()->defaultValue('0'),
            'adminid' => $this->integer(8)->unsigned()->notNull()->defaultValue('0'),
            'ip' => $this->char(15)->notNull()->defaultValue(''),
            'user_name' => $this->string(60)->notNull(),
            'user_rank' => $this->smallInteger(3)->notNull(),
            'discount' => $this->decimal(3,2)->notNull(),
            'email' => $this->string(60)->notNull(),
            'data' => $this->char(255)->notNull()->defaultValue(''),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%sessions}}');
    }
}
