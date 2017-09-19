<?php

use yii\db\Migration;

class m170908_070341_create_table_pack extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%pack}}', [
            'pack_id' => $this->smallInteger(3)->unsigned()->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'pack_name' => $this->string(120)->notNull()->defaultValue(''),
            'pack_img' => $this->string(255)->notNull()->defaultValue(''),
            'pack_fee' => $this->decimal(6,2)->unsigned()->notNull()->defaultValue('0.00'),
            'free_money' => $this->smallInteger(5)->unsigned()->notNull()->defaultValue('0'),
            'pack_desc' => $this->string(255)->notNull()->defaultValue(''),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%pack}}');
    }
}
