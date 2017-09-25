<?php

use yii\db\Migration;

class m170908_070341_create_table_collect_goods extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%collect_goods}}', [
            'rec_id' => $this->integer(8)->unsigned()->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'user_id' => $this->integer(8)->unsigned()->notNull()->defaultValue('0'),
            'goods_id' => $this->integer(8)->unsigned()->notNull()->defaultValue('0'),
            'add_time' => $this->integer(11)->unsigned()->notNull()->defaultValue('0'),
            'is_attention' => $this->smallInteger(1)->notNull()->defaultValue('0'),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%collect_goods}}');
    }
}
