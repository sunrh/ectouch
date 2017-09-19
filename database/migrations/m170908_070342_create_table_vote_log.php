<?php

use yii\db\Migration;

class m170908_070342_create_table_vote_log extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%vote_log}}', [
            'log_id' => $this->integer(8)->unsigned()->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'vote_id' => $this->smallInteger(5)->unsigned()->notNull()->defaultValue('0'),
            'ip_address' => $this->string(15)->notNull()->defaultValue(''),
            'vote_time' => $this->integer(10)->unsigned()->notNull()->defaultValue('0'),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%vote_log}}');
    }
}
