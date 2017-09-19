<?php

use yii\db\Migration;

class m170908_070341_create_table_booking_goods extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%booking_goods}}', [
            'rec_id' => $this->integer(8)->unsigned()->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'user_id' => $this->integer(8)->unsigned()->notNull()->defaultValue('0'),
            'email' => $this->string(60)->notNull()->defaultValue(''),
            'link_man' => $this->string(60)->notNull()->defaultValue(''),
            'tel' => $this->string(60)->notNull()->defaultValue(''),
            'goods_id' => $this->integer(8)->unsigned()->notNull()->defaultValue('0'),
            'goods_desc' => $this->string(255)->notNull()->defaultValue(''),
            'goods_number' => $this->smallInteger(5)->unsigned()->notNull()->defaultValue('0'),
            'booking_time' => $this->integer(10)->unsigned()->notNull()->defaultValue('0'),
            'is_dispose' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue('0'),
            'dispose_user' => $this->string(30)->notNull()->defaultValue(''),
            'dispose_time' => $this->integer(10)->unsigned()->notNull()->defaultValue('0'),
            'dispose_note' => $this->string(255)->notNull()->defaultValue(''),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%booking_goods}}');
    }
}
