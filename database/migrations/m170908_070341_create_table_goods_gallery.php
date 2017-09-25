<?php

use yii\db\Migration;

class m170908_070341_create_table_goods_gallery extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%goods_gallery}}', [
            'img_id' => $this->integer(8)->unsigned()->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'goods_id' => $this->integer(8)->unsigned()->notNull()->defaultValue('0'),
            'img_url' => $this->string(255)->notNull()->defaultValue(''),
            'img_desc' => $this->string(255)->notNull()->defaultValue(''),
            'thumb_url' => $this->string(255)->notNull()->defaultValue(''),
            'img_original' => $this->string(255)->notNull()->defaultValue(''),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%goods_gallery}}');
    }
}
