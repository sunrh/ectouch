<?php

use yii\db\Migration;

class m170908_070341_create_table_tag extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%tag}}', [
            'tag_id' => $this->integer(8)->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'user_id' => $this->integer(8)->unsigned()->notNull()->defaultValue('0'),
            'goods_id' => $this->integer(8)->unsigned()->notNull()->defaultValue('0'),
            'tag_words' => $this->string(255)->notNull()->defaultValue(''),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%tag}}');
    }
}
