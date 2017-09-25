<?php

use yii\db\Migration;

class m170908_070342_create_table_vote extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%vote}}', [
            'vote_id' => $this->smallInteger(5)->unsigned()->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'vote_name' => $this->string(250)->notNull()->defaultValue(''),
            'start_time' => $this->integer(11)->unsigned()->notNull()->defaultValue('0'),
            'end_time' => $this->integer(11)->unsigned()->notNull()->defaultValue('0'),
            'can_multi' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue('0'),
            'vote_count' => $this->integer(10)->unsigned()->notNull()->defaultValue('0'),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%vote}}');
    }
}
