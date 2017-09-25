<?php

use yii\db\Migration;

class m170908_070342_create_table_vote_option extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%vote_option}}', [
            'option_id' => $this->smallInteger(5)->unsigned()->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'vote_id' => $this->smallInteger(5)->unsigned()->notNull()->defaultValue('0'),
            'option_name' => $this->string(250)->notNull()->defaultValue(''),
            'option_count' => $this->integer(8)->unsigned()->notNull()->defaultValue('0'),
            'option_order' => $this->smallInteger(3)->unsigned()->notNull()->defaultValue('100'),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%vote_option}}');
    }
}
