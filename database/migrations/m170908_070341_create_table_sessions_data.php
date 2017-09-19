<?php

use yii\db\Migration;

class m170908_070341_create_table_sessions_data extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%sessions_data}}', [
            'sesskey' => $this->string(32)->notNull()->append('PRIMARY KEY'),
            'expiry' => $this->integer(10)->unsigned()->notNull()->defaultValue('0'),
            'data' => $this->text()->notNull(),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%sessions_data}}');
    }
}
