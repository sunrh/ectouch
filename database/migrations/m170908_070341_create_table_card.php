<?php

use yii\db\Migration;

class m170908_070341_create_table_card extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%card}}', [
            'card_id' => $this->smallInteger(3)->unsigned()->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'card_name' => $this->string(120)->notNull()->defaultValue(''),
            'card_img' => $this->string(255)->notNull()->defaultValue(''),
            'card_fee' => $this->decimal(6,2)->unsigned()->notNull()->defaultValue('0.00'),
            'free_money' => $this->decimal(6,2)->unsigned()->notNull()->defaultValue('0.00'),
            'card_desc' => $this->string(255)->notNull()->defaultValue(''),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%card}}');
    }
}
