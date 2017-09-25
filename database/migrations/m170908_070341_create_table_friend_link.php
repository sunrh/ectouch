<?php

use yii\db\Migration;

class m170908_070341_create_table_friend_link extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%friend_link}}', [
            'link_id' => $this->smallInteger(5)->unsigned()->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'link_name' => $this->string(255)->notNull()->defaultValue(''),
            'link_url' => $this->string(255)->notNull()->defaultValue(''),
            'link_logo' => $this->string(255)->notNull()->defaultValue(''),
            'show_order' => $this->smallInteger(3)->unsigned()->notNull()->defaultValue('50'),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%friend_link}}');
    }
}
