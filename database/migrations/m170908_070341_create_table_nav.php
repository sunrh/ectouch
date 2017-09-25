<?php

use yii\db\Migration;

class m170908_070341_create_table_nav extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%nav}}', [
            'id' => $this->integer(8)->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'ctype' => $this->string(10),
            'cid' => $this->smallInteger(5)->unsigned(),
            'name' => $this->string(255)->notNull(),
            'ifshow' => $this->smallInteger(1)->notNull(),
            'vieworder' => $this->smallInteger(1)->notNull(),
            'opennew' => $this->smallInteger(1)->notNull(),
            'url' => $this->string(255)->notNull(),
            'type' => $this->string(10)->notNull(),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%nav}}');
    }
}
